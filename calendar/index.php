<?php

ob_start();
session_start();
error_reporting(E_ALL);
   
include "config.php";
include "./include/local_functions.php";
include "./organizers/functions.php";
include "../calendar_include/connect.php";

function format_location($record) {
  $location_str = "";
  if($record['location_tbd'] == 'on') {
      $location_str = "TBD";
  } else {
      if($record['remote']) {
          if(!empty($record['city']))
               $location_str = $record['city'] . ", " . country_lookup($record['country']) . " &ndash; Virtual";
          elseif(!empty($record['country']) and strtoupper($record["country"]) !== "ZZ")
              $location_str = country_lookup($record['country']) . " &ndash; Virtual";
          else
              $location_str = "Virtual";
      } else {
          $location_str = $record['city'] . ", " . country_lookup($record['country']);
          if($record['streaming'])
              $location_str .= " (Streaming)";
      }
  }
  return $location_str;
}

/**
 * build_query
 *
 * @param filters: array of filters to apply to query
 *
 * @return query: string
 *
 *
 **/

// * Examples:
// * > build_query(["title" => "nanog", "range" => "all"]);
// *
// * "SELECT * FROM workshop WHERE SOUNDEX(`title`) = SOUNDEX("nanog");"
// *
// * > build_query(["title" => "nanog", "range" => "past", "country" => "US"]);
// *
// * "SELECT * FROM workshop WHERE SOUNDEX(`title`) = SOUNDEX("nanog") \
// *    AND (TO_DAYS(NOW()) - TO_DAYS(`date_end`) >= 0)
// *    AND `country`="US";"
// */
function build_query($filters) {
  $query = "SELECT * FROM `workshop`";

  $subqueries = array();

  if (array_key_exists("range", $filters)) {
    switch($filters["range"]) {
      case "past":
        array_push($subqueries, "(TO_DAYS(NOW()) - TO_DAYS(`date_end`) >= 0)");
        break;
      case "all":
        break;
      case "upcoming":
      default:
        array_push($subqueries, "(TO_DAYS(NOW()) - TO_DAYS(`date_end`) <= 0)");
        break;
    }
  } else {
    array_push($subqueries, "(TO_DAYS(NOW()) - TO_DAYS(`date_end`) <= 0)");
  }

  if (array_key_exists("region", $filters) && !array_key_exists("country", $filters)) {
    array_push($subqueries, "(`region`=\"" . $filters["region"] . "\")");
  }

  if (array_key_exists("country", $filters)) {
    array_push($subqueries, "(`country`=\"" . $filters["country"] . "\")");
  }

  if (array_key_exists("title", $filters)) {
    //array_push($subqueries, "(SOUNDEX(`title`) = SOUNDEX(\"" . $filters["title"] . "\"))");
    // Form is where upper(title) like '%STRING%'
    $title = $filters["title"];
    array_push($subqueries, "(UPPER(`title`) LIKE '%$title%')");
  }

  /**
   * return the query with an appended semicolon to be explicit when sending
   * query to PDO.
   */

  if (count($subqueries) > 0) {
    $query .= " WHERE " . join(" AND ", $subqueries);
  }

  //
  // DEBUG CODE: We should have a if $DEBUG clause...
  //
  //echo "\n".$query."\n";

  return $query . ";";
}

/**
 * get_filters
 *
 * @return filters: array of key=>value pairs of sanitized filter conditions
 *
 * Checks each supported key (region, country, title, range) and sanitizes
 * the passed value from the $_GET array.
 */
function get_filters() {
  $filters = [];

  // First, sanitize $_GET array
  foreach($_GET as $key => $value) {
    $value = preg_match("/[^\w\- ]/", "", $value);
  }

  // Date Range
  if (array_key_exists("range", $_GET)) {
    if (in_array(strtolower($_GET["range"]), ["all", "upcoming", "past"])) {
      $filters["range"] = strtolower($_GET["range"]);
    }
  }

  // Country
  if (array_key_exists("country", $_GET)) {
    $_GET["country"] = strtoupper($_GET["country"]);
    if (preg_match("/^[A-Z]{2}$/", $_GET["country"])) {
      $filters["country"] = $_GET["country"];
    }
  }

  // Region
  if (array_key_exists("region", $_GET)) {
    $_GET["region"] = strtoupper($_GET["region"]);
    if (preg_match("/^[A-Z]{2,3}$/", $_GET["region"])) {
      switch ($_GET["region"]) {
        case 'AF':
          $filters["region"] = "Africa";
          break;
        case 'AP':
          $filters["region"] = "Asia/Australia/Pacific";
          break;
        case 'AQ':
          $filters["region"] = "Antarctica";
          break;
        case 'EU':
          $filters["region"] = "Europe";
          break;
        case 'LAC':
          $filters["region"] = "Latin America/Caribbean islands";
          break;
        case 'NA':
          $filters["region"] = "North America";
          break;
        case 'GL':
          $filters["region"] = "Global";
          break;
        case 'ME':
          $filters["region"] = "Middle East";
        default:
          break;
      }
    }
  }

  if (array_key_exists("title", $_GET)) {
    $filters["title"] = $_GET["title"];
  }

  return $filters;
}

/**
 * get_sort
 *
 * @return sort: tuple containing sort key and order
 *
 * Determines what key to sort on and in what order
 */
function get_sort() {
  $sort = [];

  if (array_key_exists("date", $_GET)) {
    if (preg_match("/^(ASC|DESC)$/", $_GET["date"])) {
      $sort["key"] = "date_begin";
      $sort["by"]  = $_GET["date"] === "ASC" ? SORT_ASC : SORT_DESC;;
    }
  } elseif (array_key_exists("name", $_GET)){
    if (preg_match("/^(ASC|DESC)$/", $_GET["name"])) {
      $sort["key"] = "name";
      $sort["by"]  = $_GET["name"] === "ASC" ? SORT_ASC : SORT_DESC;;
    }
  } elseif (array_key_exists("location", $_GET)){
    if (preg_match("/^(ASC|DESC)$/", $_GET["location"])) {
      $sort["key"] = "location_fmt";
      $sort["by"]  = $_GET["location"] === "ASC" ? SORT_ASC : SORT_DESC;
    }
  } else {
    $sort["key"] = "date_begin";
    $sort["by"]  = SORT_ASC;
  }

  return $sort;
}

/**
 * draw_filter_form
 *
 * @param filters: array of filters applied to records
 *
 * Draws the form that the user can input filters into.
 */
function draw_filter_form($filters) {
//
// Define selected_country if not NULL and exists to avoid php notices and security issues
// 

	if(!isset($selected_country))
		{
			$selected_country = '';
		}
?>
  <style>
    label {
      margin: 0px 0px 0px 5px;
    }
  </style>
  <div style="/*border: 1px solid black*/; margin: auto; display: flex; justify-content: center; width: fit-content; padding: 10px">
    <form action="" method="post">
      <label for="region_filter"><b>Region: </b></label>
      <select name="region_filter" id="region_filter">
        <option value="">Select Region</option>
<?php
  $selected_region = (array_key_exists("region", $filters)) ? $filters["region"] : "";
  $regions = db_fetch_all("SELECT region,long_name FROM `regions`;", []);
  foreach ($regions as $region) {
    if ($region["long_name"] === $selected_region) {
      echo "<option value=\"" . $region["region"] . "\" selected>" . $region["long_name"] . "</option>";
    } else {
      echo "<option value=\"" . $region["region"] . "\">" . $region["long_name"] . "</option>";
    }
  }
?>
      </select>
      <label for="country_filter"><b>Country: </b></label>
<?
  $selected_country = (array_key_exists("country", $filters)) ? $filters["country"] : "";
?>
      <input type="text" maxlength="2" name="country_filter" id="country_filter" style="width: 75px;" value="<?php echo $selected_country?>" placeholder="e.g. US"/> <!-- (Two Letter ISO) -->
      <label for="range_filter"><b>Range: </b></label>
      <select name="range_filter" id="range_filter">
        <option value="">Select Range</option>
<?php
  foreach(["upcoming", "all", "past"] as $range) {
    if(array_key_exists("range", $filters) && $range === $filters["range"]) {
      echo "<option value=\"" . $range . "\" selected>" . ucfirst($range) . "</option>";
    } else {
      echo "<option value=\"" . $range . "\">" . ucfirst($range) . "</option>";
    }
  }
?>
      </select>
      <label for="title_filter"><b>Event Title: </b></label>
      <input type="text" maxlength="255" name="title_filter" id="title_filter" style="width: 120px" placeholder="e.g. NANOG" value="<?php echo array_key_exists("title", $filters) ? $filters["title"] : ""?>"/>

      <div style="margin: 10px auto 0px auto; display: flex; justify-content: center;">
        <input type="submit" value="Apply Filters" name="SUBMIT" style="margin: 0px 10px;">
        <input type="submit" value="Reset Filters" name="SUBMIT" style="margin: 0px 10px;">
      </div>
    </form>
  </div>
  <br>
<?php
}

/**
 * build_url_from_form
 *
 * Builds a URL from the filters entered into the form
 */
function build_url_from_form() {
  $url = "Location: /" . ROOT_DIR . "/?";
  if (array_key_exists("SUBMIT", $_POST)) {
    $submit_value = $_POST["SUBMIT"];
    if ($submit_value === "Apply Filters") {
      $filters = [];
      foreach ($_POST as $key => $value) {
        if ($key === "region_filter" && $value !== "") {
          array_push($filters, "region=" . $value);
        } elseif ($key === "country_filter" && $value !== "") {
          array_push($filters, "country=" . $value);
        } elseif ($key === "range_filter" && $value !== "") {
          array_push($filters, "range=" . $value);
        } elseif ($key === "title_filter" && $value !== "") {
          array_push($filters, "title=" . $value);
        }
      }

      $url .= join("&", $filters);
    }
  }
  return $url;
}

/**
 * generate_sort_atag
 *
 * @param key: key to generate a sort tag for
 * @return url: url that toggles the sort
 */
function generate_sort_atag($key, $filters, $sort) {
  $url = "?";
  $symbol = $sort["by"] === SORT_ASC ? "&#x25b2;" : "&#x25bc;";

  $label = "";
  $sort_key = "";
  if ($key === "date") {
    $sort_key = "date_begin";
    $label = "Date";
  } elseif ($key === "title") {
    $sort_key = "name";
    $label = "Title";
  } elseif ($key === "location") {
    $sort_key = "location_fmt";
    $label = "Location";
  }

  if ($sort_key === $sort["key"]) {
    if ($key === "title") {
      $key = "name";
    }
    $url .= "$key=" . ($sort["by"] === SORT_ASC ? "DESC" : "ASC");
  } else {
    if ($key === "title") {
      $key = "name";
    }
    $url .= "$key=ASC";
    $symbol = "";
  }

  $url_filters = [];
  foreach ($filters as $k => $v) {
    if ($k === "region") {
      switch ($v) {
        case "Africa":
          $v = "AF";
          break;
        case "Asia/Australia/Pacific":
          $v = "AP";
          break;
        case "Antarctica":
          $v = "AQ";
          break;
        case "Europe":
          $v = "EU";
          break;
        case "Latin America/Caribbean islands":
          $v = "LAC";
          break;
        case "North America":
          $v = "NA";
          break;
        case "Global":
          $v = "GL";
          break;
        case "Middle East":
          $v = "ME";
        default:
          break;
      }
    }
    array_push($url_filters, "$k=$v");
  }

  if (count($url_filters) > 0) {
    $url .= "&";
  }

  $url .= join("&", $url_filters);

  return "<a href=\"$url\">$label $symbol</a>";
}

/**
 * sort_records
 * @param records: array of records to be sorted
 * @param sort: sorting parameters
 *
 * @return records: sorted array of records
 */
function sort_records($records, $sort) {
  $key = $sort["key"] === "name" ? "title" : $sort["key"];
  $col = array_column($records, $key);
  if (in_array($sort["key"], ["name", "location_fmt"])) {
    array_multisort($col, $sort["by"], SORT_NATURAL|SORT_FLAG_CASE, $records);
  } else {
    array_multisort($col, $sort["by"], $records);
  }
  return $records;
}

/**
 * draw
 *
 * @param filters: array of filters applied to the records
 * @param records: array of event records
 *
 * Draws the final HTML page
 */
function draw($filters, $records, $sort) {
  include "./include/header.php";

  if(!isset($_SESSION["authenticated_user"])) {
?>
  <div id="calendarLoginContainer">
    <ul id="calendarLoginMenu">
      <li><a href="/<?=ROOT_DIR?>/scripts/register.php">Create Account</a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/recover-password.php?referrer=/<?=ROOT_DIR?>/index.php">Recover Password</a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/login.php?referrer=/<?=ROOT_DIR?>/index.php">Login</a></li>
    </ul>
  </div>
<?php
  } else {
    $user = $_SESSION["authenticated_user"];
    $user_info = db_fetch1("SELECT * FROM `user` WHERE `userid`=?", array($user));
    $user_name = $user_info["name"];
?>
  <div id="calendarLoginContainer">
    <ul id="calendarLoginMenu">
      <li><a href="/<?=ROOT_DIR?>/scripts/update.php?referrer=/<?=ROOT_DIR?>/index.php"><?php echo $user_name; ?></a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/update.php?referrer=/<?=ROOT_DIR?>/index.php">Update Profile</a></li>
      <li><a href="?logout=TRUE">Logout</a></li>
    </ul>
  </div>
<?php
  }
?>
  <table class="main">
    <tr>
      <td class="left" style="vertical-align: top;">
        <div class="sidenav" id="sidenav">
          <br>
          <p><b class="title4">Recently Added Events</b></p>
<?php
  $recently_added = db_fetch_all("SELECT * FROM `workshop` ORDER BY last_update DESC LIMIT 3", []);
  foreach ($recently_added as $row) {
    $title = $row["title"];
    $region = strtoupper($row["region"]);

    switch ($region) {
      case 'AFRICA':
        $region = "af-region.small";
        break;
      case 'ASIA/AUSTRALIA/PACIFIC':
        $region = "asia-region-small";
        break;
      case 'NORTH AMERICA':
        $region = "na-region-small";
        break;
      case 'EUROPE':
        $region = "eu-region-small";
        break;
      case 'LATIN AMERICA/CARIBBEAN ISLANDS':
        $region = "lac-region-small";
        break;
      case 'MIDDLE EAST':
        $region = "me-region-small";
        break;
      case 'GLOBAL':
        $region = "global-region-small";
        break;
      default:
        $region = "tbd-region-small";
        break;
    }

    echo "<h3 class=\"ws\">";
    echo "<a href=\"helpfiles/workshop_info_main.php?id=" . $row["id"] . "\" target=\"_blank\"><img src=\"images/regions/" . $region . ".gif\" alt=\"" . $title . "\"/>\n";
    echo "<br>\n";
    echo $title . "<br>\n";
    echo format_location($row) . "<br>";

    if ($row["date_tbd"] === "on") {
      $date_label = $row["year"] . " " . substr($row["month"], 0, 3) . " (TBD)";
    } else {
      $date_label = date_range_iso($row["date_begin"], $row["date_end"]);
    }
    echo $date_label . "</a></h3>";
  }
?>          <br>
            <a href="/<?=ROOT_DIR?>/ical/index.php">Available iCal feeds <img src="images/ics-small.gif" alt="ics" style="width: 10px; height: 12px;"></a>
            <a href="/<?=ROOT_DIR?>/rss/index.php" class="borderBottom">Available RSS feeds <img src="images/rss-icon-11x11.png" alt="rss" style="width: 10px; height: 12px;"></a>
<?php
  if(isset($_SESSION["authenticated_user"])) {
?>
            <a href="/<?=ROOT_DIR?>/organizers/create-event.php">Create a calendar entry</a>
            <a href="/<?=ROOT_DIR?>/organizers/update.php">Update a calendar entry</a>
            <br>
<?php
  } else {
    echo "<br>";
  }
?>
          <b class="title4">Event Statistics</b>
<?php
  $all_count = db_fetch1("SELECT COUNT(*) FROM `workshop`;", [])["COUNT(*)"];
  $upcoming_count = db_fetch1("SELECT COUNT(*) FROM `workshop` WHERE (TO_DAYS(NOW()) - TO_DAYS(`date_end`) <= 0);", [])["COUNT(*)"];
  $past_count = $all_count - $upcoming_count;
?>

          <table>
            <tr>
              <td>Past</td>
              <td><?php echo $past_count; ?></td>
            </tr>
            <tr>
              <td>Upcoming</td>
              <td><?php echo $upcoming_count; ?></td>
            </tr>
            <tr>
              <td>Total</td>
              <td><?php echo $all_count; ?></td>
            </tr>
          </table>
        </div>
      </td>

      <td class="content">
        <br>
        <b>Table View</b> | <a href="./map.php">Map View</a>
        <br>
        <br>
        <b class="title">Education Outreach and Training (EOT) Calendar for Internet Development</b>
        <p class="tools">
          Upcoming Events: if you know of any upcoming network-related education or training events that you think should be
          listed on this page please let us know by contacting <a href="mailto:calendar@nsrc.org">calendar@nsrc.org</a>,
          or <a href="/<?=ROOT_DIR?>/scripts/register.php">create a free account</a> on this site and then
          <a href="/<?=ROOT_DIR?>/organizers/create-event.php">create</a> or <a href="/<?=ROOT_DIR?>/organizers/update.php">update</a>
          an event yourself.
        </p>

        <p>
          If you experience problems or have any questions about this resource please contact
          <a href="mailto:calendar@nsrc.org">calendar@nsrc.org</a> for assistance. For more information please read our <a href="/<?=ROOT_DIR?>/helpfiles/Calendar_AUP.php" target="_blank">Acceptable Use Policy</a>.
        </p>
<?php

  //
  // DEBUG CODE: We should have a if $DEBUG clause...
  //
  //echo "\n TEST: ".$query."\n";

  draw_filter_form($filters);
?>
        <table style="width: 850px; padding: 0px; color: #333333; margin: auto;">
          <tr>
            <th><?php echo generate_sort_atag("date", $filters, $sort);?></th>
            <th><?php echo generate_sort_atag("title", $filters, $sort);?></th>
            <th><?php echo generate_sort_atag("location", $filters, $sort);?></th>
          </tr>
<?php
  foreach ($records as $record) {
?>
          <tr>
            <td style="width: 150px; padding: 5px 3px;">
<?php
    if ($record["date_tbd"] === "on") {
      $date_label = $record["year"] . " " . substr($record["month"], 0, 3) . " (TBD)";
    } else {
      $date_label = date_range_iso($record["date_begin"], $record["date_end"]);
    }
    echo $date_label;
?>
            </td>

            <td>
              <a href="/<?=ROOT_DIR?>/helpfiles/workshop_info_main.php?id=<?php echo $record["id"]; ?>" target="_blank">
                <?php echo $record["title"]; ?>
              </a>
            </td>

            <td style="width: 250px">
              <?php echo $record["location_fmt"]; ?>
            </td>
          </tr>
<?php
  }
?>
        </table>
        <br>
        <div style="text-align: center">
          <p>
            Hosting and maintenance of the Network Education and Training calendar is provided by the <a href="http://nsrc.org/">Network Startup Resource Center</a> at the University of Oregon. Please see our <a href="/<?=ROOT_DIR?>/helpfiles/Calendar_AUP.php" target="_blank">Acceptable Use Policy</a>.
          </p>
        </div>
      </td>
    </tr>
  </table>
<?php

  include "./include/footer.php";
}



$METHOD = $_SERVER["REQUEST_METHOD"];

$logout = strtoupper(htmlspecialchars(GET("logout")));
if(!ini_get("register_globals")) {
  $superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER);
  if(isset($_SESSION)) {
    array_unshift($superglobals, $_SESSION);
  }
  foreach ($superglobals as $superglobal) {
    extract($superglobal, EXTR_SKIP);
  }
}

if ($logout === "TRUE") {
  session_destroy();
  header("Location: /" . ROOT_DIR . "/index.php");
} else {
  if ($METHOD === "GET") {
    $filters = get_filters();
    $sort = get_sort();
    $query = build_query($filters);
    $records = db_fetch_all($query, []);
    foreach ($records as &$record) {
      $record["location_fmt"] = format_location($record);
    }
    unset($record);
    $records = sort_records($records, $sort);
    draw($filters, $records, $sort);
  } elseif ($METHOD === "POST") {
    $url = build_url_from_form();
    header($url);
  }
}

?>
