<?php

function available_action($eventid, $userid) {
    $row = db_fetch1('select privilege from user where id=?', array($userid));
    $priv = $row['privilege'];
    
    if($priv == '1' or $priv == '2') {
        $is_admin = true;
        $row = db_fetch1('select * from workshop where id=? and deleted="N" order by last_update desc', array($eventid));
    } else {
        $row = db_fetch1('select * from workshop where id=? and user_id=? and deleted="N" order by last_update desc', array($eventid, $userid));
    }

    // Old, broken code
    //$event_epoch = time(0, 0, 0,
    //    substr($row['date_end'], 5, 2), /* month */
    //    substr($row['date_end'], 8, 2), /* day */
    //    substr($row['date_end'], 0, 4)  /* year */
    //);

    // New code
    $event_month = substr($row['date_end'], 5, 2);
    $event_day = substr($row['date_end'], 8, 2);
    $event_year = substr($row['date_end'], 0, 4);
    $event_epoch = strtotime("$event_month/$event_day/$event_year");
   

    //
    // DEBUG
    //
    //echo nl2br("\nevent_epoch:= ".$event_epoch. "\n");
    //echo "\ncontents of row are: ".print_r($row)."\n";
    //echo nl2br("\nMONTH:= ".substr($row['date_end'], 5, 2)."\n");
    //echo nl2br("\nDAY:= ".substr($row['date_end'], 8, 2)."\n");
    //echo nl2br("\nYEAR:= ".substr($row['date_end'], 0, 4)."\n");
    //echo nl2br("\n*******************************************\n");

    $now_epoch = time();

    $diff_days = floor(($now_epoch - $event_epoch) / 86400);
    $diff_weeks = floor($diff_days / 7);
    $diff_months = floor($diff_weeks / 4);
    $diff_years = floor($diff_months / 12);

    $result = db_exec('select * from materials where workshop_id=? and deleted="N"', array($eventid));
    $count = 0;
    foreach($result->fetchAll() as $row)
        $count += 1;

    if($count > 0)
        $delete_action = 'N';
    else
        $delete_action = 'Y';

    if($is_admin) {
        if($delete_action == 'N')
            return 'Update';
        elseif($delete_action == 'Y')
            return 'Update or Delete';
        else
            return 'No Updates Available';
    } else {
        if($diff_days > 30)
            return 'No Updates Available';
        elseif($diff_days > 0 and $diff_days < 30)
            return 'Update';
        elseif($diff_days <= 0 and $delete_action == 'N')
            return 'Update';
        elseif($diff_days <= 0 and $delete_action == 'Y')
            return 'Update or Delete';
        else
            return 'No Updates Available';
    }
}

/* draws the NSRC header */
function draw_nsrc_header() {
    include FILE_PATH . "/include/header.php";
    echo "<link rel='stylesheet' href='./style.css' type='text/css' />";
}

/* draws the NSRC footer */
function draw_nsrc_footer() {
    include FILE_PATH . "/include/footer.php";
}

/* draws a specific module -- $section: string */
function draw_section($section) {
    include FILE_PATH . "/organizers/" . $section . ".php";
    echo "<br />";
}

/* displays search results based on user's search parameters */
function draw_search_results() {
    draw_nsrc_header();
    draw_section("event-header");
    draw_section("search");
    draw_nsrc_footer();
}

/* draw the editable form */
function draw_form_fill_in_screen() {
    draw_nsrc_header();
    draw_section("event-header");
    draw_section("search");
    draw_section("form");
    draw_nsrc_footer();
}

function draw_update_form_fill_in_screen() {
    draw_nsrc_header();
    draw_section('delete-event');
    draw_section('form');
    draw_nsrc_footer();
}

/* display event to user and have them confirm or change */
function draw_confirm_screen() {
    draw_nsrc_header();
    draw_section("confirm");
    draw_nsrc_footer();
}

/* display list of events attached to a user */
function draw_update_select_event($notify_deleted = false) {
    draw_nsrc_header();
    if($notify_deleted) {
        draw_section("notify-deleted");
    }
    draw_section("update-select-event");
    draw_nsrc_footer();
}

function draw_confirm_delete() {
    draw_nsrc_header();
    draw_section("confirm-delete");
    draw_nsrc_footer();
}

function draw_update_key($event_count, $username) {
    echo "<table class='info_table'>";

    if($event_count != 0) {
        echo "<tr><td colspan='4'><b>*Key:</b> <i>Update</i> - can make changes, but not delete entry. <i>Update or Delete</i> - can make changes and delete. <i>No Updates Avaliable</i> - cannot make any changes to the network training event or delete it.</td></tr>";
    }

    if($event_count == 0) {
        echo "<tr><td colspan='4'><span style='color: red'>No network training events found for " . $username . "</span></td></tr>";
    }

    if($event_count != 0) {
        $suffix = $event_count > 1 ? 's' : '';
        echo "<tr>";
        echo "<td colspan='4'>";
        echo "<span style='color: blue'>" . $event_count . " workshop" . $suffix . " found for " . $username . ". Select a workshop and press \"Submit Event\" to continue:</span>";
        echo "<br/>";
        echo "<input type='submit' value='Submit Event' name='SUBMIT' />";
        if(!empty($_SESSION['error']['choose'])) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            errormessage($_SESSION['error']['choose'], true);
        }
        echo "</td>";
        echo "</tr>";
    } elseif($event_count != 0) {
        echo "<tr><td colspan='4'><span style='color: #176B17'>No workshops found for " . $username . " that are available to be updated. If you have further questions please contact calendar@nsrc.org</span></td></tr>";
    }
    echo "</table>";
    echo "<br>";
}

/* draws the appropriate updating rules based on whether user is admin */
function draw_update_rules($is_admin = false) {
    echo "<table class='search_table'>";

    if($is_admin) {
        echo "<tr><td colspan='2'>";
        echo "<b style='font-size: 12pt'>Administrative User: Network Training Event Updating Rules</b>";
        echo "<ul>";
        echo "<li>You can update any network training event that has taken place, will take place or is in progress.</li>";
        echo "<li>You can delete any event as long as there are no materials associated with it.</li>";
        if($_SESSION['all_events'] == false) {
            echo "<li>By default, only the " . $_SESSION['admin']['num_upcoming'] . " upcoming events are displayed</li>";
            echo "<li>If you wish to see all " . $_SESSION['admin']['num_all'] . " events then press the \"<b>Show Everything</b>\" button.</li>";
        } else {
            echo "<li>If you wish to only see the " . $_SESSION['admin']['num_upcoming'] . " events then press the \"<b>Show Upcoming</b>\" button.</li>";
        }
        echo "<li>If you wish to assign an entry to another user, or you wish to delete an entry that has associated materials, please contact calendar@nsrc.org for help.</li>";
        echo "</ul>";

        echo "</td></tr>";

        echo "<tr><td>";
        echo "<b style='color: blue'>Choose the event you wish to update, then press the 'Submit Event' button:</b>";
        echo "</td>";
        echo "<td>";
        if($_SESSION['all_events']) {
            echo "<form method='POST' action=''>";
            echo "<input type='submit' value='Show Upcoming' name='SUBMIT' />";
            echo "</form>";
        } else {
            echo "<form method='POST' action=''>";
            echo "<input type='submit' value='Show Everything' name='SUBMIT' />";
            echo "</form>";
        }
        echo "</td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "<td colspan='2'><b style='font-size: 12pt'>Standard User: Network Training Event Updating Rules</b>";
        echo "<ul>";
        echo "<li>If the network training event has not yet taken place, you can update any aspect of it.</li>";
        echo "<li>If the event has not yet taken place you can delete the entry as long as no associated materials have been uploaded for the event. If materials have been uploaded and you still wish to delete the event entry entirely, contact calendar@nsrc.org for help.</li>";
        echo "<li>If the event took place less than 30 days ago, you can update any aspect of it, but you cannot delete the entry.</li>";
        echo "<li>If the event took place more than 30 days ago, you cannot make any changes to the event. If you still need to make changes, please contact calendar@nsrc.org for help.</li>";
        echo "<li>If you wish to assign an entry to another user, please contact calendar@nsrc.org for help.</li>";
        echo "</ul>";
        echo "<b style='color: blue'>Choose the event you wish to update, then press the 'Submit Event' button:</b>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

function do_delete() {
    $result = db_update('delete from workshop where id=?', array($_SESSION['eventid']));
    if(!$result[0])
        $_SESSION['error']['delete'] = "Unable to delete event record, unexpected error. Please contact calendar@nsrc.org for help. Please provide us with this error message: <br>" . htmlentities($result[1], ENT_QUOTES);
}

/* helper function to display error messages, can break below or above it */
function errormessage($msg, $break_below = false) {
    if($break_below == false)
        echo "<br/>\n";
    echo "<b style='color: red'>";
    echo "Error: " . $msg;
    echo "</b>";
    if($break_below)
        echo "<br/>\n";
}

/* helper function to get values out of the form based on a key */
function form_value($key) {
    if(!empty($_SESSION['form'][$key])) {
        echo "<b style='color: blue'>";
        echo $_SESSION['form'][$key];
        echo "</b>";
    }
}

/* display whether the event is remote or physical */
function display_attendance_type() {
    if($_SESSION['form']['attendance_type'] == 'virtual') {
        echo "<b style='color: blue'>Virtual</b>";
    } elseif($_SESSION['form']['attendance_type'] == 'in-person') {
        echo "<b style='color: blue'>In-Person";
        if($_SESSION['form']['has_online'] == 'on') {
            echo " (w/ Online Component)";
        }
        echo "</b>";
    }
}

/* display the regions that the user selected */
function display_regions() {
    if(!empty($_SESSION['form']['region1'])) {
        form_value('region1');

        if(!empty($_SESSION['form']['region2'])) {
            echo "<br>";
            form_value('region2');
        }

        if(!empty($_SESSION['form']['region3'])) {
            echo "<br>";
            form_value('region3');
        }
    }
}

/* displays city, country based on whether location is tbd */
/* if city/country is empty, formatting is fixed accordingly */
function display_city_country() {
    if(!empty($_SESSION['form']['location_tbd'])) {
        echo "<span style='color: blue'><b>TBD</b> (To Be Determined)</span>";
    } else {
        if(!empty($_SESSION['form']['city'])) {
            form_value('city');
        }

        if(!empty($_SESSION['form']['city']) && !empty($_SESSION['form']['country'])) {
            echo ", ";
        }

        form_value('country');
    }
}

/* converts a month integer to a month string (1-based indexing) */
function month_int_to_str($a) {
    if($a < 1 or $a > 12) {
        return 'INVALID MONTH';
    }
    $months = array(
        1 => "January",
        2 => "February",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8 => "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December"
    );

    return $months[$a];
}

/* displays the begin date of an event based on whether dates are TBD */
function display_begin_date() {
    echo "<b style='color: blue'>";

    if(!empty($_SESSION['form']['date_tbd'])) {
        echo month_int_to_str($_SESSION['form']['begin_month']) . ", " . $_SESSION['form']['begin_year'];
    } else {
        echo month_int_to_str($_SESSION['form']['begin_month']) . " " . $_SESSION['form']['begin_day'] . ", " . $_SESSION['form']['begin_year'];
    }

    echo "</b>";
}

/* displays the end date of an event based on whether dates are TBD */
function display_end_date() {
    echo "<b style='color: blue'>";

    if(!empty($_SESSION['form']['date_tbd'])) {
        echo "TBD";
    } else {
        echo month_int_to_str($_SESSION['form']['end_month']) . " " . $_SESSION['form']['end_day'] . ", " . $_SESSION['form']['end_year'];
    }

    echo "</b>";
}

/* gets a language name from the db based on its id */
function get_language($id) {
    $row = db_fetch1('select * from languages where id=?', array($id));
    return $row['language'];
}

/* displays a language name based on its id */
function display_language($key) {
    if(!empty($_SESSION['form'][$key])) {
        $language = get_language($_SESSION['form'][$key]);
        echo "<b style='color: blue'>" . $language . "</b>";
    }
}

/* populates a select list with all known regions in our db */
function populate_regions($arr, $key) {
    $result = db_exec('select * from regions order by region', array());
    while($row = $result->fetch()) {
        $region = $row['long_name'];
        if($_SESSION[$arr][$key] == $region) {
            echo "<option value='$region' selected>$region</option>";
        } else {
            echo "<option value='$region'>$region</option>";
        }
    }
}

/* populates a list of days -- pretty self explanatory */
function populate_days($key) {
    for($day = 1; $day <= 31; $day++) {
        if($_SESSION['form'][$key] == $day) {
            echo "<option value='$day' selected>$day</option>";
        } else {
            echo "<option value='$day'>$day</option>";
        }
    }
}

/* populates a select list with all the months */
function populate_months($key) {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $i = 0;
    foreach($months as $month) {
        $i++;
        if($_SESSION['form'][$key] == $i) {
            echo "<option value='$i' selected>$month</option>";
        } else {
            echo "<option value='$i'>$month</option>";
        }
    }
}

/* populates a select list based on current year + 10 */
function populate_years($arr, $key) {
    $now = date('Y') + 10;
    for($year = $now; $year >= 1987; $year--) {
        if($_SESSION[$arr][$key] == $year) {
            echo "<option value='$year' selected>$year</option>";
       } else {
            echo "<option value='$year'>$year</option>";
       }
    }
}

   
/* populates a select list with all known languages in our db */
function populate_languages($key) {
    $i = 0;
    $result = db_exec('select * from languages order by id', array());
    while($row = $result->fetch()) {
        $language = $row['language'];

        if($_SESSION['form'][$key] == $language) {
            echo "<option value='$language' selected>$language</option>";
        } else {
            echo "<option value='$language'>$language</option>";
        }
    }
}

// Populates the form for the event the user is attempting to edit.
// This plus draw_update_form_fill_in_screen() create the actual
// page that's loaded when a user is editing or attempting to delete
// an event.
function populate_session_form($eventid) {
    $event = db_fetch1("select * from workshop where id=?", array($eventid));
    unset($_SESSION['form']);

    $begin_year = substr($event['date_begin'], 0, 4);
    $end_year = substr($event['date_end'], 0, 4);

    $begin_month = (int) substr($event['date_begin'], 5, 2);
    $end_month = (int) substr($event['date_end'], 5, 2);

    $begin_day = (int) substr($event['date_begin'], 8, 2);
    $end_day = (int) substr($event['date_end'], 8, 2);

    $_SESSION['form'] = array(
       'title' => htmlentities($event['title'], ENT_QUOTES),
       'url' => $event['url'],
       'location_tbd' => $event['location_tbd'] == 'on' ? 'on' : '',
       'country' => $event['country'] == 'ZZ' ? '' : $event['country'],
       'city' => htmlentities($event['city'], ENT_QUOTES),
       'region1' => $event['region'],
       'region2' => $event['region_secondary'],
       'region3' => $event['region_terciary'],
       'begin_day' => $begin_day,
       'begin_month' => $begin_month,
       'begin_year' => $begin_year,
       'date_tbd' => $event['date_tbd'] == 'on' ? 'on' : '',
       'end_day' => $end_day,
       'end_month' => $end_month,
       'end_year' => $end_year,
       'language1' => $event['language1'],
       'language2' => $event['language2'],
       'language3' => $event['language3'],
       'other_language' => htmlentities($event['other_language'], ENT_QUOTES),
       'location' => $event['location'],
       'url_secondary' => $event['url_secondary'],
       'contact_name' => $event['contact_name'],
       'contact_email' => $event['contact_email'],
       'text' => htmlentities($event['comment'], ENT_QUOTES),
       'training' => $event['training'],
       'attendance_type' => $event['remote'] ? 'virtual' : 'in-person',
       'has_online' => $event['streaming'] ? 'on' : '',
       27
    );

}

function reset_error() {
    unset($_SESSION['error']);
    $_SESSION['error'] = array(
        'count' => 0,
        'title' => '',
        'url' => '',
        'attendance_type' => '',
        'country' => '',
        'city' => '',
        'region' => '',
        'date' => '',
        'language' => '',
        'other_language' => '',
        'url_secondary' => '',
        'location' => '', /* ambiguous naming -- refers to venue */
        'contact_name' => '',
        'contact_email' => '',
        'text' => '',
        'location_tbd' => '',
        'date_tbd' => '',
        'training' => '',
        'choose' => '',
        'delete' => '',
        19
    );

}

function update_session_form() {
    unset($_SESSION['form']);
    $_SESSION['form'] = array(
        'title' => htmlspecialchars($_POST['title']),
        'url' => htmlspecialchars($_POST['url']),
        'location_tbd' => htmlspecialchars($_POST['location_tbd']),
        'country' => htmlspecialchars($_POST['country']),
        'city' => htmlspecialchars($_POST['city']),
        'region1' => htmlspecialchars($_POST['region1']),
        'region2' => htmlspecialchars($_POST['region2']),
        'region3' => htmlspecialchars($_POST['region3']),
        'begin_day' => htmlspecialchars($_POST['begin_day']),
        'begin_month' => htmlspecialchars($_POST['begin_month']),
        'begin_year' => htmlspecialchars($_POST['begin_year']),
        'date_tbd' => htmlspecialchars($_POST['date_tbd']),
        'end_day' => htmlspecialchars($_POST['end_day']),
        'end_month' => htmlspecialchars($_POST['end_month']),
        'end_year' => htmlspecialchars($_POST['end_year']),
        'language1' => htmlspecialchars($_POST['language1']),
        'language2' => htmlspecialchars($_POST['language2']),
        'language3' => htmlspecialchars($_POST['language3']),
        'other_language' => htmlspecialchars($_POST['other_language']),
        'location' => htmlspecialchars($_POST['location']), /* ambiguous naming -- refers to venue */ /* TODO: normalize naming conventions -- requires refactor */
        'url_secondary' => htmlspecialchars($_POST['url_secondary']),
        'contact_name' => htmlspecialchars($_POST['contact_name']),
        'contact_email' => htmlspecialchars($_POST['contact_email']),
        'text' => htmlspecialchars($_POST['text']),
        'training' => htmlspecialchars($_POST['training']),
        'attendance_type' => htmlspecialchars($_POST['attendance_type']),
        'has_online' => htmlspecialchars($_POST['has_online']),
        27
    );
}

function is_valid_cc($cc) {
    $result = db_exec('select * from country where country_code = ?', array($cc));
    return $result->rowCount() == 1;
}

/* get the long name of a country from its two-digit ISO country code */
function get_country_name($cc) {
    $result = db_exec('select * from country where country_code = ?', array($cc));
    return $result['country_name'];
}

function check_event() {
    /* get session values, for easier reading */
    $form = $_SESSION['form'];
    $error = $_SESSION['error'];
    $error_count = 0;

    /* title */
    if(empty($form['title'])) {
        $error_count += 1;
        $error['title'] = 'you need to enter a title for your workshop.';
    } elseif(!checkforalpha($form['title'])) {
        $error_count += 1;
        $error['title'] = "the title \"" . $form['title'] . "\" does not appear to contain valid characters (a-zA-Z0-9-_@!.'\"). Please enter another title.";
    }

    /* url */
    $form['url'] = filter_var($form['url'], FILTER_SANITIZE_URL);
    if(empty($form['url'])) {
        $error_count += 1;
        $error['url'] = 'you need to enter a url for your workshop';
    } elseif(!filter_var($form['url'], FILTER_VALIDATE_URL)) {
        $error_count += 1;
        $error['url'] = "\"" . $form['url'] . "\" does not appear to be a valid url.";
    }

    /* attendance type */
    if(empty($form['attendance_type'])) {
        $error_count += 1;
        $error['attendance_type'] = 'you must select an attendance type.';
    }

    /* country */
    if(($form['attendance_type'] == 'in-person') && empty($form['location_tbd'])) {
        if(empty($form['country'])) {
            $error_count += 1;
            $error['country'] = 'since your event is in-person and you have not selected "To Be Determined", you must enter a country code. Click the link above for a list of two-digit ISO country codes.';
        }
    } 

    if(!empty($form['country']) && !is_valid_cc($form['country'])) {
        $error_count += 1;
        $error['country'] = "\"" . $form['country'] . "\" is not a valid two-digit ISO country code. Please use the link above to look up a valid country code.";
    }

    /* city */
    if(($form['attendance_type'] == 'in-person') && empty($form['location_tbd'])) {
        if(empty($form['city'])) {
            $error_count += 1;
            $error['city'] = 'since your event is in-person and you have not selected "To Be Determined", you must enter a city';
        } elseif(!checkforalpha($form['city'])) {
            $error_count += 1;
            $error['city'] = "the city \"" . $form['city'] . "\" does not appear to contain valid characters (a-zA-Z0-9-_@!.'\"). Please enter another city.";
        }
    } elseif($form['attendance_type'] == 'virtual' && empty($form['country'])) {
        if(!empty($form['city'])) {
            $error_count += 1;
            $error['city'] = 'Country is required with a city. Please provide one.';
        }
    }

    /* regions */
    /* TODO: confirm logic */
    if(empty($form['location_tbd'])) {
        if(empty($form['region1'])) {
            $error_count += 1;
            $error['region'] = 'you have not indicated a primary region. Please select one in the [Primary Region] drop-down list.';
        } else {
            if($form['region1'] == $form['region2'] or $form['region1'] == $form['region3']) {
		// Updated to $error_count from $error - HA on 17 June 2021
                $error_count += 1;
                $error['region'] = 'you have chosen identical regions. Please choose distinct regions. If only one region is chosen, please use the [Primary Region] drop-down list to indicate such, and leave the others blank.';
            } elseif(!empty($form['region2']) && $form['region2'] == $form['region3']) {
		// Updated to $error_count from $error - HA on 17 June 2021
                $error_count += 1;
                $error['region'] = 'you have chosen identical regions. Please choose distinct regions. If only one region is chosen, please use the [Primary Region] drop-down list to indicate such, and leave the others blank.';
            }
        }
    }

    /* date */
    if(empty($form['date_tbd'])) {
        if(empty($form['begin_day']) or
           empty($form['begin_month']) or
           empty($form['begin_year']) or
           empty($form['end_day']) or
           empty($form['end_month']) or
           empty($form['end_year'])) {
            $error_count += 1;
            $error['date'] = 'please select all values for both start and end date, OR select "Final dates to be determined".';
        } else {
            $begin_date_str = $form['begin_year'] . "-". sprintf("%02d", $form['begin_month']) . "-" . sprintf("%02d", $form['begin_day']);
            $end_date_str = $form['end_year'] . "-". sprintf("%02d", $form['end_month']) . "-" . sprintf("%02d", $form['end_day']);
            $begin_date = date_create($begin_date_str);
            $end_date = date_create($end_date_str);
            $diff = date_diff($begin_date, $end_date, 0);
            $sign = $diff->format("%R");
            $days = (int)($diff->format("%a"));
            if($sign == "-") {
                $error_count += 1;
                $error["date"] = "Please select a valid date range; your range appears to be negative!";
            } else {
                // Event lasts longer than one month
                if($days > 31) {
                    $error_count += 1;
                    $error["date"] = "Your date range covers more than one month, is that right? If so, please contact <a href=\"mailto:calendar@nsrc.org\">calendar@nsrc.org</a> for assistance.";
                }
            }
        }
    } else {
        if(empty($form['begin_month']) or empty($form['begin_year'])) {
            $error_count += 1;
            $error['date'] = 'you must at least select a starting month and year for your event (this can be changed later).';
        }
    }


    /* language */
    if(empty($form['language1']) && empty($form['other_language'])) {
        $error_count += 1;
        $error['language'] = 'you have not indicated a primary language. Please do so using the Primary Language drop down list or entering one into the Language Not Listed text box.';
    } else {
        if(!empty($form['language1'])) {
            if($form['language1'] == $form['language2'] or $form['language1'] == $form['language3']) {
                $error_count += 1;
                $error['language'] = 'you have selected duplicate languages. If only one language is to be used, please indicate using the Primary Language drop down list or entering one in the Language Not Listed text box.';
            } elseif(!empty($form['language2']) && $form['language2'] == $form['language3']) {
                $error_count += 1;
                $error['language'] = 'you have selected duplicate languages. If only one language is to be used, please indicate using the Primary Language drop down list or entering one in the Language Not Listed text box.';
            }
        }
    }

    /* specific location */
    if(!empty($form['location']) && !checkforalpha($form['location'])) {
        $error_count += 1;
        $error['location'] = "\"" . $form['location'] . "\" does not appear to only contain valid characters (a-zA-Z0-9-@!.'\"). Please enter another location.";
    }

    /* secondary url */
    if(!empty($form['url_secondary']) && !filter_var($form['url_secondary'], FILTER_VALIDATE_URL)) {
        $error_count += 1;
        $error['url_secondary'] = "\"" . $form['url_secondary'] . "\" does not appear to be a valid URL.";
    }

    /* name */
    if(!empty($form['contact_name']) && !checkforalpha($form['contact_name'])) {
        $error_count += 1;
        $error['contact_name'] = "\"" . $form['contact_name'] . "\" does not appear to only contain valid characters (a-zA-Z0-9-_'\"@!.). Please enter another name.";
    }

    /* email */
    if(!empty($form['contact_email']) && !filter_var($form['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $error_count += 1;
        $error['contact_email'] = "\"" . $form['contact_email'] . "\" does not appear to be a valid email address.";
    }

    /* comment */
    if(!empty($form['text']) && !checkforalpha($form['text'])) {
        $error_count += 1;
        $error['text'] = "\"" . $form['text'] . "\" does not appear to only contain valid characters (a-zA-Z0-9-_'\"@!.). Please enter your comments again with valid characters.";
    }


    /* not sure if php assigns arrays by reference or value, so we force a reference update on the session variable, just in case */
    $error['count'] = $error_count;
    $_SESSION['error'] = $error;
    $_SESSION['form'] = $form;
}

function get_form_values_array() {
    $row = db_fetch1("select * from user where userid=?", array($_SESSION['authenticated_user']));
    $userid = $row['id'];
    $username = $row['name'];
    $creator = $username;

    /* now, we can grab all the form values to insert into the database */

    /* abstraction for easier reading */
    $form = $_SESSION['form'];

    /* title */
    if(empty($form['title']))
        die('Title cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    $title = $form['title'];

    /* url */
    if(empty($form['url']))
        die('URL cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    $url = $form['url'];

    /* remote */
    if(empty($form['attendance_type']))
        die('Attendance Type cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    $remote = $form['attendance_type'] == 'virtual' ? 1 : 0;
    
    /* streaming */
    $streaming = (!$remote && $form['has_online'] == 'on') ? 1 : 0;

    /* location_tbd */
    $location_tbd = $form['location_tbd'] == 'on' ? 'on' : 'no';

    /* country */
    if(empty($form['country'])) {
        if($location_tbd == 'on')
            $country = '';
        elseif($remote)
            $country = '';
        else
            die('Country cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    } else {
        $country = $location_tbd == 'on' ? '' : $form['country'];
    }

    /* city */
    if(empty($form['city'])) {
        if($location_tbd == 'on' or $remote)
            $city = '';
        else
            die('City cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    } else {
        $city = $location_tbd == 'on' ? '' : $form['city'];
    }

    /* primary region */
    if(empty($form['region1'])) {
        if($location_tbd == 'on' or $remote)
            $region = '';
        else
            die('Primary Region cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    } else {
        $region = $location_tbd == 'on' ? '' : $form['region1'];
    }

    /* secondary region */
    if(!empty($form['region2']))
        $region_secondary = $location_tbd == 'on' ? '' : $form['region2'];
    else
        $region_secondary = '';

    /* tertiary region */
    if(!empty($form['region3']))
        $region_tertiary = $location_tbd == 'on' ? '' : $form['region3'];
    else
        $region_tertiary = '';

    /* date tbd */
    $date_tbd = $form['date_tbd'] == 'on' ? 'on' : 'no';

    /* begin date */
    if(empty($form['begin_year']) or empty($form['begin_month']))
        die('Start Year and Month cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');

    if($date_tbd == 'on')
        $date_begin = sprintf("%04d-%02d-%02d", $form['begin_year'], $form['begin_month'], 1);
    elseif(empty($form['begin_day']))
        die('Start Day cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    else
        $date_begin = sprintf("%04d-%02d-%02d", $form['begin_year'], $form['begin_month'], $form['begin_day']);

    /* end date */
    if($date_tbd == 'on')
        $date_end = $date_begin;
    elseif(empty($form['end_year']) or empty($form['end_month']) or empty($form['end_day']))
        die('End Date cannot be empty. If this is an error, please contact calendar@nsrc.org for help.');
    else
        $date_end = sprintf("%04d-%02d-%02d", $form['end_year'], $form['end_month'], $form['end_day']);

    /* primary language */
    if(!empty($form['language1']))
        $language1 = $form['language1'];
    else
        $language1 = '';

    /* secondary language */
    if(!empty($form['language2']))
        $language2 = $form['language2'];
    else
        $language2 = '';

    /* tertiary language */
    if(!empty($form['language3']))
        $language3 = $form['language3'];
    else
        $language3 = '';

    /* other language */
    if(!empty($form['other_language']))
        $other_language = $form['other_language'];
    else
        $other_language = '';

    /* venue */
    if(!empty($form['location']))
        $venue = $form['location'];
    else
        $venue = '';

    /* secondary url */
    if(!empty($form['url_secondary']))
        $url_secondary = $form['url_secondary'];
    else
        $url_secondary = '';

    /* contact name */
    if(!empty($form['contact_name']))
        $contact_name = $form['contact_name'];
    else
        $contact_name = '';

    /* contact email */
    if(!empty($form['contact_email']))
        $contact_email = $form['contact_email'];
    else
        $contact_email = '';

    /* comment */
    if(!empty($form['text']))
        $comment = $form['text'];
    else
        $comment = '';

    /* default to approved event */
    $approved = 'Y';

    /* build the directory */
    $year = $form['begin_year'];
    $month = month_int_to_str($form['begin_month']);
    $directory = $_SERVER[DOCUMENT_ROOT] . "/data/" . $year . "/";

    $last_update = date("Y-m-d H:i:s");

    $values = array(
        $userid,
        $title,
        $date_begin,
        $date_end,
        $url,
        $url_secondary,
        $country,
        $city,
        $region,
        $region_secondary,
        $region_tertiary,
        $language1,
        $language2,
        $language3,
        $other_language,
        $venue,
        $contact_name,
        $contact_email,
        $comment,
        $year,
        $month,
        $approved,
        $directory,
        $creator,
        $last_update,
        $date_tbd,
        $location_tbd,
        $streaming,
        $remote
    );

    if(isset($_SESSION['is_update']))
        array_push($values, $_SESSION['eventid']);

    return $values;
}

function get_finalize_query() {
    if(isset($_SESSION['is_update'])) {
        $query = "update workshop ";
        $query .= "set user_id=?, ";
        $query .= "title=?, ";
        $query .= "date_begin=?, ";
        $query .= "date_end=?, ";
        $query .= "url=?, ";
        $query .= "url_secondary=?, ";
        $query .= "country=?, ";
        $query .= "city=?, ";
        $query .= "region=?, ";
        $query .= "region_secondary=?, ";
        $query .= "region_terciary=?, ";
        $query .= "language1=?, ";
        $query .= "language2=?, ";
        $query .= "language3=?, ";
        $query .= "other_language=?, ";
        $query .= "location=?, ";
        $query .= "contact_name=?, ";
        $query .= "contact_email=?, ";
        $query .= "comment=?, ";
        $query .= "year=?, ";
        $query .= "month=?, ";
        $query .= "approved=?, ";
        $query .= "directory=?, ";
        $query .= "creation_user=?, ";
        $query .= "last_update=?, ";
        $query .= "date_tbd=?, ";
        $query .= "location_tbd=?, ";
        $query .= "streaming=?, ";
        $query .= "remote=? ";
        $query .= "where id=?";
        return $query;
    }

    else {
        /* TODO: clean up */
        return "INSERT INTO workshop (user_id, title, date_begin, date_end, url, url_secondary, country, city, region, region_secondary, region_terciary, language1, language2, language3, other_language, location, contact_name, contact_email, comment, year, month, approved, directory, creation_user, last_update, date_tbd, location_tbd, streaming, remote, lat, lon)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.0, 0.0)";
    }
}
?>
