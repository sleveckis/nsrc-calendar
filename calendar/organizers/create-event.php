<?php
//create-event.php

session_start();
ob_start();
$session = session_id();

/* must be set before includes */
$FORM_TYPE = "Submit New Event";
$CONFIRM_TYPE = 'Go Back';

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

if($logout == true) {
    session_destroy();
    header('Location: /organizers/create-event.php');
}

/* first check that the user is logged in */
if(isset($_SESSION['authenticated_user'])) {
    /* create value arrays and store in session */
    $submit_value = $_POST['SUBMIT'];
    /* only grab form values if user has clicked a submit button of some kind (i.e. not on inital load) */
    if($submit_value == 'View All' or
       $submit_value == 'Search For Event' or
       $submit_value == 'Submit New Event') {
       /* contains all the data the user submits in the form */
       update_session_form();
    }

    /* array that holds error messages for different form values */
    reset_error();

    /* if the user is submitting a new form, check event for errors */
    if($submit_value == 'Submit New Event') {
        check_event();
    }
}

/* draw the appropriate screen based on where in the "wizard" the user is */
if(!isset($_SESSION['authenticated_user'])) {
    header("Location: " . $server . "/" . ROOT_DIR . "/scripts/login-required.php?requested_page=/" . ROOT_DIR . "/organizers/" . $filename);
}

elseif($submit_value == 'View All') {
    $_SESSION['view_all'] = true;
    draw_search_results();
}

elseif($submit_value == 'Search For Event') {
    $_SESSION['view_all'] = false;
    $_SESSION['do_search'] = true;
    $_SESSION['search'] = array(
        'by_date' => htmlspecialchars($_POST['by_date']),
        'by_region' => htmlspecialchars($_POST['by_region']),
        'substring' => htmlspecialchars($_POST['substring']),
        3
    );
    draw_search_results();
}

elseif($submit_value == 'Finalize Event') {
    include FILE_PATH . '/organizers/finalize.php';
    finalize_event();
}

elseif($submit_value == 'Go Back') {
    draw_form_fill_in_screen();
}

elseif($submit_value == 'Submit New Event') {
    if($_SESSION['error']['count'] > 0) {
        draw_form_fill_in_screen();
    } elseif($_SESSION['error']['count'] == 0) {
        draw_confirm_screen();
    } else {
        echo "<b>Unexpected error. Please contact <a href='mailto:calendar@nsrc.org'>calendar@nsrc.org</a> for help.</b><br />";
    }
}

else {
    draw_form_fill_in_screen();
}

?>
