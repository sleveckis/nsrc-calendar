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

function draw() {
  include "./include/header.php";

  if(!isset($_SESSION["authenticated_user"])) {
?>
  <div id="calendarLoginContainer">
    <ul id="calendarLoginMenu">
      <li><a href="/<?=ROOT_DIR?>/scripts/register.php">Create Account</a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/recover-password.php?referrer=/<?=ROOT_DIR?>/map.php">Recover Password</a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/login.php?referrer=/<?=ROOT_DIR?>/map.php">Login</a></li>
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
      <li><a href="/<?=ROOT_DIR?>/scripts/update.php?referrer=/<?=ROOT_DIR?>/map.php"><?php echo $user_name; ?></a></li>
      <li><a href="/<?=ROOT_DIR?>/scripts/update.php?referrer=/<?=ROOT_DIR?>/map.php">Update Profile</a></li>
      <li><a href="/<?=ROOT_DIR?>/map.php?logout=TRUE">Logout</a></li>
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
  $recently_added = db_fetch_all("SELECT*  FROM `workshop` ORDER BY last_update DESC LIMIT 3", []);
  foreach($recently_added as $row) {
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

      <td style="vertical-align: top;" class="content">
        <br>

        <a href="./index.php">Table View</a> | <b>Map View</b>
        <br>
        <br>

        <iframe src="https://pages.uoregon.edu/infographics/dev/NSRC/EventMap/calendarMap.html" frameborder="0"></iframe>
        <style>
        iframe {
          width: 100%;
          height: 100vh;
          max-height: 685px;
          box-shadow: none !important;
          border-radius: 8px;
          border: none;
          margin-bottom: 10px;
        }
        </style>
      </td>
    </tr>
  </table>
<?php

  include "./include/footer.php";
}


$logout = strtoupper(htmlspecialchars(GET("logout")));
if(!ini_get("register_globals")) {
  $superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER);
  if(isset($_SESSION)) {
    array_unshift($superglobals, $_SESSION);
  }

  foreach($superglobals as $superglobal) {
    extract($superglobal, EXTR_SKIP);
  }
}

if($logout === "TRUE") {
  session_destroy();
  header("Location: /" . ROOT_DIR . "/map.php");
}

draw();

?>
