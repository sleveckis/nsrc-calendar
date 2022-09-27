<?php
ob_start();
session_start();
$session = session_id();

//
// Updates September 2008 by Anna Secka Saine
// Updates November 2008 by Hervey Allen
//

include '../config.php';
// Several housecleaning functions that we use throughout.
include FILE_PATH . '/include/local_functions.php';
// Used to validate email entered in the form. Long function.
include FILE_PATH . "/include/checkemail.php";

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as
// subroutines. Eventually most of the code in main should go here.
include FILE_PATH . '/include/checkupdates.php';

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . '/../calendar_include/connect.php';



// Emulate register_globals on **************************************** KN
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
// END emulate register globals *************************************** KN





//
// Only report errors, not warnings.
//
error_reporting(E_ERROR);

function update_page($authed_user,
		     $user_name,
		     $sessionID,
		     $update_formvalues_array,
		     $update_error_array)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - User Profile Update";
$header_heading = "User Profile Update";
$header_referrer = FILE_PATH . "/scripts/update.php";
include FILE_PATH . '/include/header.php';

//
// No check for authed user as you must/should be logged in just to get here - even if
// you load this form directly.
//

?>

<tr><td>
<br />
<strong>User Profile Update</strong>: Below are the values entered when creating your user profile. You may update any of this information, except for your userid. If you need to change your userid please contact calendar@nsrc.org via email.

<?php
    //
    // Warn userif not using ssl (https) port to log in.
    //

    if($_SERVER['SERVER_PORT'] != 443)
        {
            $referrer = $update_formvalues_array['referrer'];
            ?>
            <br />
            <br />
            <font color="#ff0000"><strong>Security Warning:</strong></font> This page is insecure
            Use the secure User Profile Update page available <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/<?=ROOT_DIR?>/scripts/update.php?referrer=<?php echo $referrer?>">here</a>.
            <br />
            <?php
        }
?>

<table border="0" cellpadding="0" cellspacing="0" width="742">
<tr><td colspan="4">
<br>
<form method='POST' action='/<?=ROOT_DIR?>/scripts/update.php'>

<?php
  // If there are errors on the page, then let the user know.
  if((isset($sessionID)) && ($update_error_array["count"] > 0 ))
  {
  echo "<p><b><font face='Verdana' size='3' color='#ff0000'>Errors were detected!<br>\n";
  echo "Please check your form below and correct the indicated errors as necessary.</b></font>\n";
  }

?>


<!-- userid -->

        <tr>
          <td width='15%' valign='top'><font size='2'>

* <a href="/<?=ROOT_DIR?>/helpfiles/Register_login.php" target="_blank">Userid</a>:</font></td>
<td valign='top'>
<font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
    <b><?php echo $update_formvalues_array['userid1']?></b> (Contact calendar@nsrc.org to change userid)
</font></font>
</td></tr>

<!-- space -->

<tr>
<td valign='top' colspan='2'><br></td>
</tr>

<!-- firstname -->

        <tr>
          <td width='15%' valign='top'><font size='2'>

    * <a href="/<?=ROOT_DIR?>/helpfiles/Register_firstname.php" target="_blank">First Name(s)</a>:</font></td>
    <td><small>Your first name. This will be used for materials you submit or update, so please use a form of your first name that you would wish others to see.

<?php
    if((isset($sessionID)) && (!empty($update_error_array["first_name"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $update_error_array["first_name"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>
        <tr>
          <td width='0' valign='top'></td>
          <td valign='top'><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='first_name' size='44'  value='".$update_formvalues_array["first_name"]."' maxlength='254'>\n";
    }
    else
      {
	echo "<input type='text' name='first_name' size='44'  value='' maxlength='254'>\n";
      }
?>

</font></td>
        </tr>

<!-- lastname -->

        <tr>
          <td width='15%' valign'top'><font size='2'>
* <a href="/<?=ROOT_DIR?>/helpfiles/Register_lastname.php" target="_blank">Last Name(s)</a>:</font></td>
<td valign='top'><small>Your last name. This will be used for materials you submit or update. Both your first and last name(s) will be displayed.

<?php
    if((isset($sessionID)) && (!empty($update_error_array["last_name"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $update_error_array["last_name"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>
        <tr>
          <td width='0' valign='top'></td>
          <td><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='last_name' size='44'  value='".$update_formvalues_array["last_name"]."' maxlength='254'>\n";
    }
    else
      {
	echo "<input type='text' name='last_name' size='44'  value='' maxlength='254'>\n";
      }
?>

</font></td>
        </tr>

<!-- space -->

<tr>
<td valign='top' colspan='2'><br></td>
</tr>

<!-- password -->

        <tr>
          <td width='15%' valign='top'><font size='2'>

* <a href="/<?=ROOT_DIR?>/helpfiles/Register_password.php" target="_blank">Password</a>:</font></td>
          <td><small>Your password is not displayed for security purposes. If you wish to change your password please choose a new password and type it twice; please use at least six(6) characters.

<?php
    if((isset($sessionID)) && (!empty($update_error_array["password"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $update_error_array["password"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>
        <tr>
          <td width='0'></td>
          <td><font size='2'>

<?php
    // Password values are encrypted if we are here due to bad password change attempt.
    if (isset($sessionID)) {
      echo "<input type='password' name='password1' size='20'  value='".$update_formvalues_array["password1"]."'></font><br><font size='2'>\n";
      echo "<input type='password' name='password2' size='20'  value='".$update_formvalues_array["password2"]."'>\n";
    }
    else
      {
	echo "<input type='password' name='password1' size='20'  value=''>\n";
	echo "<input type='password' name='password2' size='20'  value=''>\n";
      }
?>

</font></td>
        </tr>


<!-- Password Recovery -->

        <tr>
          <td width='15%' valign='top'><font size='2'>
    * <a href="/<?=ROOT_DIR?>/helpfiles/Register_recover.php" target="_blank">Password</a> <br>&nbsp;&nbsp;&nbsp;<a href="/<?=ROOT_DIR?>/helpfiles/Register_recover.php" target="_blank">Recovery</a>:</font></td>
          <td valign='top'><small>Please enter descriptive text for a question that you can answer in case you
          forget your password. Then, enter the answer in the second box below. Your answer is not case sensitive, but    spaces and punctuation are included.</font>
<?php
    if((isset($sessionID)) && (!empty($update_error_array["pw_recover"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $update_error_array["pw_recover"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>

        <tr>
          <td width='0'></td>
          <td valign='top'><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to
    // begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='pw_recover_question' size='32'  value='".$update_formvalues_array["pw_recover_question"]."' maxlength='96'> <font size='1'><b>Question</b> (Example: 'My favorite food')</font>\n";
      echo "<br>\n";
      echo "<input type='text' name='pw_recover_answer' size='32'  value='".$update_formvalues_array["pw_recover_answer"]."' maxlength='96'> <font size='1'><b>Answer</b> (Example: 'Oysters')</font>\n";
    }
    else
      {
	echo "<input type='text' name='pw_recover_question' size='32'  value='' maxlength='96'> <font size='1'>b>Question</b> (Example: 'My favorite food')</font>\n";
	echo "<br>\n";
	echo "<input type='text' name='pw_recover_answer' size='32'  value='' maxlength='96'> <font size='1'><b>Answer</b> (Example: 'Oysters')</font>\n";
      }

?>

</font>
</td></tr>
</table>


</td></tr>
<tr><td align="left">
<br />
<hr>
<input type='submit' value='Submit Updates' name='SUBMIT'>
</form>

<!-- End of form items -->

<br />
<br />
<br />
<strong>Privacy Notice:</strong> We do not display user email addresses on static pages or to anyone who is not a registered user logged into this site. In addition, we do not share, sell or make available your information to outside parties.
</td></tr>
</table>


<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

 //$our_filename = $_SERVER[DOCUMENT_ROOT]."/calendar/scripts/update.php";
 include FILE_PATH . '/include/footer.php';
?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

    } // end function update_page
?>









<?php
function valid_update($authedUser,
                      $user_name,
                      $sessionID,
                      $update_formvalues_array,
                      $update_error_array)
{


// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar User Profile Update - Verification of Data";
$header_heading = "User Profile Update Verify";
$header_referrer = FILE_PATH . "/scripts/update.php";
include FILE_PATH . '/include/header.php';

echo"<form method='POST' action='/".ROOT_DIR."/scripts/update.php'>\n";
?>

<tr><td>
<br />
<strong>Verify that your updated user information is correct</strong>
<p>
<strong>Enter your original password for these updates to be made:</strong>
</p>


<table>
<tr><td width="120" valign="middle" align="left">

<?php
    if(!empty($update_error_array["user_pw"]))
    {
      echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='3'><font color='#ff0000'>\n";
      echo "<b>PASSWORD:</b>\n";
      echo "</font></font></font>\n";
    }
    else
    {
      echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='3'>\n";
      echo "<b>PASSWORD:</b>\n";
      echo "</font></font>\n";
    }
?>

</td>
<td align="left" width="100" valign="middle">
<input type='password' size='15' value='' name='user_pw'>
</td>
<td width="522">

<?php
    if(!empty($update_error_array["user_pw"]))
    {
      echo "<font color='#ff0000'>" .$update_error_array['user_pw']. "</font>\n";
    }
    else
    {
      echo "&nbsp;\n";
    }
?>

</td></tr>
</table>

<br />

<table border="0" cellpadding="0" cellspacing="0" width="742">
<tr><td>
<p>
<input type='submit' value='Update' name='SUBMIT'> <font color='#176b17'><b>your user information if it looks correct. Remember to enter your original password above.</b></font>
<p>
<input type='submit' value='Go Back' name='SUBMIT'> <font color='#176b17'><b>if your user information is <i>not</i> correct and you wish to make corrections.</b></font>
<p>
<div align="center">

<?php
    if(empty($update_formvalues_array['referrer']))
        {
            echo "<b>Note:</b> upon pressing 'Update' you will be redirected to the home page for this site.\n";
        }
    else
        {
            $redirect_url = "http://" .$_SERVER['SERVER_NAME'].$update_formvalues_array['referrer'];
            echo "<b>Note:</b> upon pressing 'Update' you will be redirected to the page " .$redirect_url. ".\n";
        }
?>

</div>
<hr>
<strong>Required Information</strong>
</td>
</tr>
</table>

</form>

<table border="0" cellpadding="0" cellspacing="0" width="742">

<!-- userid -->

<tr>
<td width='25%' valign='top'><font size='2'>* Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$update_formvalues_array['userid1']. "</font></b>\n";
?>
</td></tr>

<!-- password recovery question-->

<tr>
<td width='25%' valign='top'><font size='2'>* Password recovery question:</td>
<td width='2'></td valign='top'>
<td valign='top'><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$update_formvalues_array['pw_recover_question']. "</font></b>\n";
?>
</td></tr>

<!-- password recovery answer -->

<tr>
<td width='25%' valign='top'><font size='2'>* Password recovery answer:</td>
<td width='2'></td valign='top'>
<td valign='top'><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$update_formvalues_array['pw_recover_answer']. "</font></b>\n";
?>
</td></tr>

<!-- firstname -->

<tr>
<td width='25%' valign='top'><font size='2'>* First Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$update_formvalues_array['first_name']. "</font></b>\n";
?>
</td></tr>


<!-- lastname -->

<tr>
<td width='25%' valign='top'><font size='2'>* Last Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$update_formvalues_array['last_name']. "</font></b>\n";
?>
</td></tr>

<!-- password -->

<tr>
<td width='25%' valign='top'><font size='2'>* Password:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>Password not displayed for security reasons.</font></b>\n";
?>
</td></tr>
</table>

</td></tr>
</table>

<br />

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    //$our_filename = $_SERVER[DOCUMENT_ROOT]."/calendar/scripts/update.php";
    include FILE_PATH . '/include/footer.php';
?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

} // end function valid_update

?>



<?php
function finish_update($authed_user,
                     $user_name,
                     $sessionID,
                     $update_formvalues_array,
                     $update_error_array)
{
  // First, let's build up some additional variables as needed.
  //
  // Since we have a bunch of optional information the user may or may not choose
  // to enter, we need to be careful about how we set information in the db.
  //
  // Here is what the user table in the isoc database looks like, as of June 29, 2003.
  // The id field is set to auto increment, so we will let MySQL do this for us and skip
  // explicitly setting this field.
  //
  // The other fields have already been checked, or are preset to NULL, so we will simply
  // assign each field the value it contains from the form at this point - except for the
  // user password, which must be encrypted and hashed.
  //
  // Additional explanation of some fields that need to be set, or may need explanation:
  //
  // * id, userid, password, name, and phone are all required fields.
  // * country is the two letter iso code. We use the country table to look up the
  //   correct name of the country when needed.
  // * By default new user signups are approved. We may include code to check this
  //   and/or admin code to change this if needed.
  // * Privilege level of 0 by default - means general user. Other priv levels, like
  //   1 and 2 for admin use and admin interface.
  // * Creation date is now and will be set as a timestamp.
  // * Creation user in this form is the user. If user created from an admin interface,
  //   then it will be the admin user who is logged in at the time.
  // * Last update will be today, but set as a datetime record. This will change any time
  //   an update is made by the user or an admin user.
  // * Update user will be either the user if they update their record, or the admin user
  //   that is logged in if they update a user's record.
  //
/*
+-----------------------------------+---------------------+------+-----+---------------------+----------------+
| Field                             | Type                | Null | Key | Default             | Extra          |
+-----------------------------------+---------------------+------+-----+---------------------+----------------+
| id                                | bigint(20) unsigned |      | PRI | NULL                | auto_increment |
| userid                            | varchar(96)         |      | MUL |                     |                |
| password                          | varchar(255)        |      |     |                     |                |
| pw_recover_question               | varchar(255)        |      |     |                     |                |
| pw_recover_answer                 | varchar(255)        |      |     |                     |                |
| first_name                        | varchar(255)        |      |     |                     |                |
| last_name                         | varchar(255)        |      |     |                     |                |
| name                              | varchar(255)        |      |     |                     |                |
| phone                             | varchar(255)        | YES  |     | NULL                |                |
| language1                         | varchar(255)        | YES  |     | NULL                |                |
| language2                         | varchar(255)        | YES  |     | NULL                |                |
| language3                         | varchar(255)        | YES  |     | NULL                |                |
| country                           | char(2)             | YES  |     | NULL                |                |
| city                              | varchar(255)        | YES  |     | NULL                |                |
| title                             | varchar(255)        | YES  |     | NULL                |                |
| employer                          | varchar(255)        | YES  |     | NULL                |                |
| expertise_organizer               | enum('Y','N')       | YES  |     | N                   |                |
| expertise_speaker                 | enum('Y','N')       | YES  |     | N                   |                |
| expertise_instructor              | enum('Y','N')       | YES  |     | N                   |                |
| expertise_instructor_routing      | enum('Y','N')       | YES  |     | N                   |                |
| expertise_instructor_IP           | enum('Y','N')       | YES  |     | N                   |                |
| expertise_instructor_applications | enum('Y','N')       | YES  |     | N                   |                |
| expertise_instructor_wireless     | enum('Y','N')       | YES  |     | N                   |                |
| expertise_additional              | varchar(255)        | YES  |     | NULL                |                |
| travel                            | enum('Y','N')       | YES  |     | N                   |                |
| remote_preparation                | enum('Y','N')       | YES  |     | N                   |                |
| remote_coordination               | enum('Y','N')       | YES  |     | N                   |                |
| comment                           | text                | YES  |     | NULL                |                |
| approved                          | enum('Y','N')       | YES  |     | Y                   |                |
| privilege                         | char(1)             |      |     | 0                   |                |
| creation_date                     | datetime            |      |     | 0000-00-00 00:00:00 |                |
| creation_user                     | varchar(96)         | YES  |     | NULL                |                |
| last_update                       | timestamp(14)       | YES  |     | NULL                |                |
| update_user                       | varchar(96)         | YES  |     | NULL                |                |
| deleted                           | enum('Y','N')       | YES  |     | N                   |                |
+-----------------------------------+---------------------+------+-----+---------------------+----------------+
  */

 // *Warning*! Note the 'password1' string. We could change this to just
 // be 'password' earlier in the process, but if we are here then in the
 // original form password1 = password2, thus you can choose either...

if(!empty($update_formvalues_array["password1"]))
    {
    $md5_password = "MD5:" . strtoupper(md5($update_formvalues_array["password1"]));
    }

//
// Grab/create a few values for later user. For admin tools we can do stuff like
// set approve, delete, privilege, update_user, etc...
//

 $update_user =  $update_formvalues_array["userid1"];
 $username = $update_formvalues_array["first_name"]. " " .$update_formvalues_array["last_name"];

  $row_for_id = db_fetch1("select * from user where userid= ?", array($authed_user));
  $record_id = $row_for_id['id'];

//
// Update the user record item-by-item. Do error reporting as well.
//

$error_during_update = 0;

//
// password
//

if(!empty($update_formvalues_array["password1"]))
    {
        $result = db_update("update user set password = ? where id = ?", array($md5_password, $record_id));

        if(!$result[0])
            {
                $error_during_update = $error_during_update + 1;

                 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
                 echo "The error that occured was:<center>\n";
                 echo $result[1]. "</center><p><hr>\n";
            }
    }

//
// pw_recover_question
//

$pw_recover_question = $update_formvalues_array["pw_recover_question"];
$result = db_update("update user set pw_recover_question = ? where id = ?", array($pw_recover_question, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }

//
// pw_recover_answer
//

$pw_recover_answer = $update_formvalues_array["pw_recover_answer"];
$result = db_update("update user set pw_recover_answer = ? where id = ?", array($pw_recover_answer, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }

//
// first_name
//

$first_name = $update_formvalues_array["first_name"];
$result = db_update("update user set first_name = ? where id = ?", array($first_name, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }

//
// last_name
//

$last_name = $update_formvalues_array["last_name"];
$result = db_update("update user set last_name = ? where id = ?", array($last_name, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }

//
// name
//

$result = db_update("update user set name = ? where id = ?", array($username, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }


//
// available
//

// $available = $update_formvalues_array["available_button"];
$available = 'N';
$result = db_update("update user set available = ? where id = ?", array($available, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }



//
// update_user
//

$result = db_update("update user set update_user = ? where id = ?", array($update_user, $record_id));

if(!$result[0])
    {
        $error_during_update = $error_during_update + 1;

	 echo "<p><center><b>Error creating your updated user record. Please contact calendar@nsrc.org for help.</b></center><p>\n";
         echo "The error that occured was:<center>\n";
	 echo $result[1]. "</center><p><hr>\n";
    }


if($error_during_update == 0)
    {
        // redirect to home page. updates are done.

        $redirect_url = "http://" .$_SERVER['SERVER_NAME'].$update_formvalues_array['referrer'];
        //$referrer_url =

	unset($_SESSION['update_formvalues_array']);
	unset($_SESSION['update_error_array']);
        if(empty($referrer))
            {
                header("Location: http://".$_SERVER['SERVER_NAME']."/" . ROOT_DIR . "/index.php");
            }
        else
            {
                header("Location: http://".$_SERVER['SERVER_NAME']."$redirect_url");
            	//header("Location: " . $redirect_url);
	    }
    }
        else
    {
            echo "<p>&nbsp;</p><b>SUBMISSION ERRORS DETECTED</b><p>\n";
            echo "<center><b>There were/was " .$error_during_update. " number of error(s) while updating your user record. Please ontact calendar@nsrc.org for help.</b></center><p>\n";
            echo "If you can include any of the specific error messages you received in an email that will help us to resolve this problem much more quickly. Thank you.<p>\n";
            echo "<center>[<a href='/" . ROOT_DIR . "/index.php'>Return to Network Startup Resource Center Network Education Calendar Home Page</a>]</center><br>\n";
            echo "</body></html>\n";
    }

 } // End function finish_update
?>



<?php
//
// Main
//
if ($_SERVER["HTTPS"] != 'on')
    {
        //header("Location: https://" .$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?referrer=$referrer");
    }

//  if (session_is_registered("authenticated_user")) *** KN
if (isset($_SESSION["authenticated_user"]))  //  ** KN
      {
	$authed_user = $_SESSION["authenticated_user"];

	$row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
	$user_name = $row_name['name'];
      }
if ($logout == TRUE)
  {
    session_destroy();
    header("Location: http://".$_SERVER['SERVER_NAME']."/" . ROOT_DIR . "/index.php");
  }

//
// User is not logged in. They must be logged in to upload materials, thus
// send them to the error page, where they'll be asked to log in and then
// redirected back to this page.
//

//  elseif(!session_is_registered("authenticated_user"))   *** KN
elseif (!isset($_SESSION["authenticated_user"]))
{
  header("Location: http://".$_SERVER['SERVER_NAME']."/" . ROOT_DIR . "/scripts/errors.php?id=4");
}
elseif($SUBMIT == 'Update')
{
        $user_pw = htmlspecialchars($_POST["user_pw"]);

        $pw_result = verify_password($authed_user, $user_pw);

        if($pw_result == 'TRUE')
            {
                finish_update($authed_user,
                            $user_name,
                            $PHPSESSID,
                            $update_formvalues_array,
                            $update_error_array);
            }
        elseif($pw_result == 'FALSE')
            {
                $update_error_array['user_pw'] = "Password invalid. Please re-enter your password.";

                valid_update($authed_user,
                            $user_name,
                            $PHPSESSID,
                            $update_formvalues_array,
                            $update_error_array);
            }
        elseif($pw_result == 'EMPTY')
            {
                $update_error_array['user_pw'] = "Password empty. Please enter your password before pressing Update.";

                valid_update($authed_user,
                            $user_name,
                            $PHPSESSID,
                            $update_formvalues_array,
                            $update_error_array);
            }
        else
            {
                        $update_error_array['user_pw'] = "Unknown error. Please contact calendar@nsrc.org for help.";

                valid_update($authed_user,
                            $user_name,
                            $PHPSESSID,
                            $update_formvalues_array,
                            $update_error_array);
            }
       }

elseif($SUBMIT == 'Go Back')
{
        update_page($authed_user,
	      $user_name,
	      $PHPSESSID,
	      $update_formvalues_array,
	      '');
}

elseif ($SUBMIT == 'Submit Updates')
{

 $update_formvalues_array = array('userid1' => $update_formvalues_array["userid1"]
			   , 'userid2' => $update_formvalues_array["userid2"]
			   , 'first_name' => htmlspecialchars($_POST["first_name"])
			   , 'last_name' => htmlspecialchars($_POST["last_name"])
			   , 'password1' => htmlspecialchars($_POST["password1"])
			   , 'password2' => htmlspecialchars($_POST["password2"])
			   , 'pw_recover_question' => htmlspecialchars($_POST["pw_recover_question"])
			   , 'pw_recover_answer' => htmlspecialchars($_POST["pw_recover_answer"])
			   , 'available_button' => htmlspecialchars($_POST["available_button"])
			   , 'referrer' => $update_formvalues_array['referrer']
			   , 10);

  // *** This is critical! ***
  // If you _don't_ make this global, and you _don't_ register this
  // array with the session, then you cannot go back and redisplay
  // the contents of what the user filled out at a later time.

  $_SESSION['update_formvalues_array'] = $update_formvalues_array;

    $update_error_array = array('count' => '0'
		       , 'userid' => ''
		       , 'first_name'  => ''
		       , 'last_name'  => ''
		       // There are two password form fields. This is set if they
		       // don't match.
		       , 'password' => ''
		       , 'pw_recover' => ''
		       , 'available_button' => ''
		       , 7);

  $_SESSION['update_error_array'] = $update_error_array;
  $update_error_array = checkforupdateerrors($update_error_array, $update_formvalues_array);

    if($update_error_array["count"] > 0)
        {
    update_page($authed_user,
	      $user_name,
	      $PHPSESSID,
	      $update_formvalues_array,
	      $update_error_array);
        }
    else
        {
         valid_update($authed_user,
	             $user_name,
	             $PHPSESSID,
	             $update_formvalues_array,
                     $update_error_array);
       }


}

elseif(isset($_SESSION['authenticated_user']))
{
  $authed_user = $_SESSION["authenticated_user"];

  $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
  $user_name = $row_name['name'];

  $update_formvalues_array = array('userid1' => $row_name["userid"]
			   , 'userid2' => $row_name["userid"]
			   , 'first_name' => $row_name["first_name"]
			   , 'last_name' => $row_name["last_name"]
			   , 'password1' => ''
			   , 'password2' => ''
			   , 'pw_recover_question' => $row_name["pw_recover_question"]
			   , 'pw_recover_answer' => $row_name["pw_recover_answer"]
			   , 'available_button' => $row_name["available"]
			   , 'referrer' => $referrer
			   , 10);

  $PHPSESSID = TRUE;

  $_SESSION['update_formvalues_array'] = $update_formvalues_array;

  update_page($authed_user,
	      $user_name,
	      $PHPSESSID,
	      $update_formvalues_array,
	      '');
}
else
 {
   echo "<html><head><title='Network Startup Resource Center Network Education Calendar: Unexpected Error'></title><body>\n";
   echo "<b>Unexpected error. Please contact calendar@nsrc.org for help.</b>\n";
   echo "</body></html>\n";
 }

?>
