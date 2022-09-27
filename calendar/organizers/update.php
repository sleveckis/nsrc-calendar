<?php
//update.php

session_start();
ob_start();
$session = session_id();

/* must be set before includes */
$FORM_TYPE = "Update Event";
$CONFIRM_TYPE = 'No, Return';

include '../config.php';
include FILE_PATH . '/include/local_functions.php';
include FILE_PATH . '/organizers/functions.php';
include FILE_PATH . '/include/checkuser.php';
include FILE_PATH . '/../calendar_include/connect.php';

error_reporting(E_ERROR);

$server = "https://" . $_SERVER['SERVER_NAME'];
$path = "/calendar/organizers/";
$filename = basename($_SERVER['PHP_SELF']);
$url = $server . $path . $filename;

$submit_options = ["Submit Event", "Delete Event", "Yes, Delete", "No, Go Back", "Go Back", "Update Event", "Finalize Event", "No, Return"];

if($logout == true) {
    session_destroy();
    header("Location: /organizers/update.php");
}

/* Clean session if POST does not match any valid logic branches */
if(isset($_SESSION['authenticated_user']) and !in_array($_POST['SUBMIT'], $submit_options)) {
    unset($_SESSION['form']);
    unset($_SESSION['error']);
    reset_error();
}

if(!isset($_SESSION['authenticated_user'])) {
    header("Location: ". $server . "/" . ROOT_DIR . "/scripts/login-required.php?requested_page=/" . ROOT_DIR . "/organizers/" . $filename);
} else {
    if($_POST['SUBMIT'] == 'Submit Event') {
        reset_error();
        if(!isset($_POST['available'])) {
            $_SESSION['error']['choose'] = 'You did not pick an event to update.';
            $_SESSION['all_events'] = false;
            draw_update_select_event();
        } else {
            $_SESSION['eventid'] = $_POST['available'];
            populate_session_form($_POST['available']);
	    // where UPDATE and DELETE buttons are generated
	    // inside organizers/functions.php, which calls
	    // organizers/form.php (button code at the bottom)
            draw_update_form_fill_in_screen();
        }
    }

    elseif($_POST['SUBMIT'] == 'Delete Event') {
        draw_confirm_delete();
    }

    elseif($_POST['SUBMIT'] == 'Go Back') {
        $_SESSION['all_events'] = false;
        draw_update_select_event();
    }

    // DELETE event code... 
    elseif($_POST['SUBMIT'] == 'Yes, Delete') {
        do_delete();
        $_SESSION['all_events'] = false;
        draw_update_select_event(true);
        unset($_SESSION['form']);
    }

    elseif($_POST['SUBMIT'] == 'No, Go Back') {
        draw_update_form_fill_in_screen();
    }

    // result of "Update Event" button click
    elseif($_POST['SUBMIT'] == 'Update Event') {
        update_session_form();
        check_event();
        if($_SESSION['error']['count'] > 0)
	    // in organizers/functions.php
            draw_update_form_fill_in_screen();
	    

        elseif($_SESSION['error']['count'] == 0) {
            draw_confirm_screen();
        } else {
            echo "<b>Unexpected error. Please contact calendar@nsrc.org for help.</b>";
        }
    }

    elseif($_POST['SUBMIT'] == 'Finalize Event') {
        $_SESSION['is_update'] = true;
        include FILE_PATH . '/organizers/finalize.php';
        finalize_event();
        unset($_SESSION['is_update']);
    }

    elseif($_POST['SUBMIT'] == 'No, Return') {
        draw_update_form_fill_in_screen();
    }

    elseif($_POST['SUBMIT'] == 'Show Everything') {
        $_SESSION['all_events'] = true;
        draw_update_select_event();
    }

    elseif($_POST['SUBMIT'] == 'Show Upcoming') {
        $_SESSION['all_events'] = false;
        draw_update_select_event();
    }

    else {
        $_SESSION['all_events'] = false;
        draw_update_select_event();
    }
}

?>
