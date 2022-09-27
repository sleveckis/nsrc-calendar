<?php
ob_start();
session_start();
$session = session_id();

// For ROOT_DIR and FILE_PATH
include "../config.php";

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Used to validate email entered in the form. Long function.
include FILE_PATH . "/include/checkemail.php";

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as
// subroutines. Eventually most of the code in main should go here.
include FILE_PATH . "/include/checkuser.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

// Emulate register_globals on     ***********************  KN
if (!ini_get('register_globals')) {
    // needs to be in order in php.ini, default is EGPCS
    $superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER);
    if (isset($_SESSION)) {
        array_unshift($superglobals, $_SESSION);
    }
    foreach ($superglobals as $superglobal) {
        extract($superglobal, EXTR_SKIP);
    }
}

// Set some default values for variables to avoid php notices

if (!isset($SUBMIT)) { $SUBMIT = ""; }
if (!isset($authed_user)) { $authed_user =""; }
if (!isset($user_name)) { $user_name = ""; }
// END emulate register globals ******************************  KN

function errors_page($authed_user,
		     $user_name,
		     $error_id)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center (NSRC) Network Education " . ROOT_DIR . " - ERROR";
$header_heading = "ERROR ".$error_id;
$header_referrer = "/" . ROOT_DIR . "/scripts/errors.php";
include FILE_PATH . "/include/header.php";

?>

<tr><td class="contents">

<?php

   switch ($error_id)
     {
       case 1:
       // Error ID 1
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ":</b><p>Before you can register a userid you must first \n";
       echo "log off from the system. Please press the \"Logout\" button above, and then  \n";
       echo "click <a href='https://" . $_SERVER['SERVER_NAME'] . "/" . ROOT_DIR . "/scripts/register.php'>here</a> to return to the user registration page.</p>\n";
       echo "</font></font>\n";
       break;

       case 2:
       // Error ID 2
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p>\n";
       echo "<p>Before you can upload a file to the site you must \n";
       echo "log in. To do this click <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/instructors/upload.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the Instructors upload page. If you have not \n";
       echo "registered on this site, then go to the  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a> \n";
       echo "to create a user account. Once you are registered go back to the Instructor upload \n";
       echo "pages to continue with this process.</p>\n";
	echo "<p>We apologize for this inconvenience, but for items that allow you to create content on the site a login is required to avoid problems with spammers. Thank you for your understanding.</p>\n";
       echo "</font></font>\n";
       break;

       case 3:
       // Error ID 3
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p> Unknown error. Perhaps your session has expired. if so, you \n";
       echo "will need to log back in to the system. You can do this by clicking  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php'>here</a></p>.\n";
       echo "</font></font>\n";
       break;

       case 4:
       // Error ID 4
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>Before you can update your user profile you must be logged in. \n";
       echo "To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/scripts/update.php'>here</a>. Once logged in you will \n";
       echo "be redirected to the user profile update page.</p>\n";
       echo "</font></font>\n";
       break;

       case 5:
       // Error ID 5
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "Before you can create a workshop entry you must \n";
       echo "log in. To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/organizers/create.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the Organizers workshop creation page. If you have not \n";
       echo "registered on this site, then go to the  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a> \n";
       echo "to create a user account. Once you are registered go back to the Organizers workshop creation \n";
       echo "pages to continue with this process.</p>\n";
       echo "</font></font>\n";
       break;

        case 6:
       // Error ID 6
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "Before you can search for a workshop entry you must \n";
       echo "log in. To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/search/search-workshops.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the workshop search page. If you have not \n";
       echo "registered on this site, then go to the  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a> \n";
       echo "to create a user account. Once you are registered go back to the Workshp Search \n";
       echo "pages to continue.</p>\n";
       echo "</font></font>\n";
       break;

        case 7:
       // Error ID 7
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/admin/index.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

        case 8:
       // Error ID 8
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not have admin privileges. You must be a site administrator to view this page.<p>\n";
       echo "You can return to the ISOC Workhop Resource Centre home page <a href='/index.php'>here</a>.\n";
       echo "</font></font></p>\n";
       break;

        case 9:
       // Error ID 9
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/admin/index.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

        case 10:
       // Error ID 10
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You are already logged in.</p><p>\n";
       echo "This implies that you know your password. If for some reason you still need to recover your password, then click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/recover-password.php?logout=TRUE'>here to logout</a> and start the password recovery/reset process.</p>\n";
       echo "</font></font>\n";
       break;

       case 11:
       // Error ID 11
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "Before you can update a workshop entry you must \n";
       echo "log in. To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/" . ROOT_DIR . "/organizers/update.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the Organizers workshop update page. If you have not \n";
       echo "registered on this site, then go to the <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a> \n";
       echo "to create a user account. But, you will not have any workshops to update at this point, so you \n";
       echo "may wish to create a workshop entry starting <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/organizers/create.php'>here</a>.\n";
       echo "</font></font></p>\n";
       break;

       case 12:
       // Error ID 12
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "Before you can update materials you must \n";
       echo "log in. To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/instructors/update-description.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the Instructors materials update page. If you have not \n";
       echo "registered on this site, then go to the  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a>\n";
       echo "to create a user account.</p>\n";
       echo "</font></font>\n";
       break;

        case 13:
       // Error ID 13
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You are not logged in.</p><p>\n";
       echo "You must log in as the site administrator to access this page. If you have access to this account, \n";
       echo "then you can log in  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/phpMyAdmin-2.5.3/'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

        case 14:
       // Error ID 14
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "Incorrect Userid.<p>\n";
       echo "You must log in as the site administrator to access this page. If you have access to this account, \n";
       echo "then you can log in  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/phpMyAdmin-2.5.3/'>here</a>. Note, \n";
       echo "you will be asked to log out first upon clicking the link above.</p>\n";
       echo "</font></font>\n";
       break;

        case 15:
       // Error ID 15
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page with an administrative account. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/isoc-db.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

        case 16:
       // Error ID 16
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page with an administrative account. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/cpw.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

        case 17:
       // Error ID 17
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><p>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "Incorrect Userid.</p><p>\n";
       echo "You must log in as the site administrator to access this page. If you have access to this account, \n";
       echo "then you can log in  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/cpw.php'>here</a>. Note, \n";
       echo "you will be asked to log out first upon clicking the link above.</p>\n";
       echo "</font></font>\n";
       break;

        case 18:
       // Error ID 18
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page with an administrative account. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/pinfo.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

       case 19:
       // Error ID 19
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "Before you can create an event entry you must \n";
       echo "log in. To do this click  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/organizers/create-event.php'>here</a>. \n";
       echo "Log in, and you will be sent back to the Event Creation page. If you have not \n";
       echo "registered on this site, then go to the  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/register.php'>Registration Page</a> \n";
       echo "to create a user account. Once you are registered go back to the Event Creation \n";
       echo "pages to continue with this process.</p>\n";
       echo "</font></font>\n";
       break;

        case 20:
       // Error ID 20
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected.</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo "<b>Error ID " .$error_id. ": </b></p><p>\n";
       echo "You do not appear to be logged in.</p><p>\n";
       echo "You must be logged in to view this page with an administrative account. You can log in for this page  <a href='https://" .$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/scripts/login.php?referrer=/calendar/admin/reports.php'>here</a>.</p>\n";
       echo "</font></font>\n";
       break;

       default:
       echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'>\n";
       echo "<b>Unknown Error or Condition:</b></font></font></font>\n";
	echo "<p><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>You may have clicked the Logout or Login button(s) on this page while reading an error message, otherwise if you need help, or wish to report this \n";
       echo "error, please send email \n";
       echo "to <a href='mailto:calendar@nsrc.org'>calendar@nsrc.org</a>.</p>\n";
       echo "</font></font>\n";
     }

?>
</td></tr>
</table>

<br>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.


	//$our_filename =  "$_SERVER[DOCUMENT_ROOT]/" . ROOT_DIR . "/scripts/errors.php";
	include FILE_PATH . "/include/footer.php";
?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

    } // end function search_page
?>

<?php
//
// Main
//

if(!isset($id))
    {
        $id = null;
    }

if ($logout == 'TRUE')
    {
        session_destroy();
        errors_page(null,
                    null,
                    $keep_id);
    }
else
    {
        errors_page(null,
                    null,
                    $id);
    }

?>
