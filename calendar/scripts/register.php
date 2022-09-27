<?php
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
}
$sessionID = session_id();

// register.php
//
// Hervey Allen for ISOC, Summer 2003
//
// Make sure to add in a generic include file that does full checking to
// verify that the session and user are valid.
//
// Updates April 2007.
// Updates September 2008 by Anna Secka Saine
//
// Include email notification of new registration to site admin
// calendar@nsrc.org. This is to keep us aware of potential
// spammer registrations, which have been an issue starting in
// Nov. 2006.
//

// Set debug output on or off
$DEBUG = FALSE;
//$FILEPATH =  realpath(dirname(__FILE__));
//$FILEPATH = "/var/www/calendar";
include "../config.php";

// Several housecleaning functions that we use throughout.
//include "$FILEPATH/include/local_functions.php";
include FILE_PATH . "/include/local_functions.php";

// Used to validate email entered in the form. Long function.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkemail.php";
//include "$FILEPATH/include/checkemail.php";
include FILE_PATH . "/include/checkemail.php";

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as
// subroutines. Eventually most of the code in main should go here.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkuser.php";
//include "$FILEPATH/include/checkuser.php";
include FILE_PATH . "/include/checkuser.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

//
// For now, we turn off warnings
//

error_reporting(E_ERROR);

// Coming soon general authentication checking before entering the form.
// ********************



// Emulate register_globals on  ********************************  KN
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
// END emulate register globals *********************************** KN





//
// Function signup_page.
//

function signup_page($authedUser,
		     $sessionID,
		     $register_error_array,
		     $register_formvalues_array)

{



// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

    $header_title = "Network Startup Resource Center Network Education Calendar - User Registration";
    $header_heading = "User Registration";
    $header_referrer = "/" . ROOT_DIR . "/scripts/register.php";
    //include '/var/www/calendar/include/header.php';
    include FILE_PATH . "/include/header.php";
    if($DEBUG){
      print "<br />*****************************<br />";
      echo("\n\r".FILE_PATH."\n\r");
      print "<br />*****************************<br />";
      print_r($_SERVER);
      print "<br />*****************************<br />";
      print_r($_POST);
      print "<br />*****************************<br />";
      print_r($_SESSION);
      print "<br />*****************************<br />";
      print_r($register_formvalues_array);
      print "<br />*****************************<br />";
      print "<br />*****************************<br />";
    }
?>

<tr><td>
   A userid and password are required on this site if you plan to add or update workshop materials or events. All items below are required to register.
<br>

<?php
    //
    // Warn userif not using ssl (https) port to log in.
    //

    if($_SERVER['SERVER_PORT'] != 443)
        {
            ?>
            <br>
            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
            <font color="#ff0000"><b>Security Warning:</b></font> This page is insecure
            Use the secure User Registration page available <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/<?=ROOT_DIR?>/scripts/new_register.php">here</a>.
            </font></font>
            <?php
        }
?>

<hr>
<b class="bold">Required Information</b>
</font></font>
<br>&nbsp;



<form method='POST' action='/<?=ROOT_DIR?>/scripts/register.php'>

<?php
  // If there are errors on the page, then let the user know.
  if((isset($sessionID)) && ($register_error_array["count"] > 0 ))
  {
  echo "<b><font face='Verdana' size='3' color='#ff0000'>Errors were detected!<br>\n";
  echo "Please check your form below and correct the indicated errors as necessary.</b></font>\n";
  }

?>

<table border="0" cellpadding="0" cellspacing="0" width="742">

<!-- userid -->

        <tr>
          <td width='15%' valign='top'><font size='2'>
    * <a href="/<?=ROOT_DIR?>/helpfiles/Register_login.php" target="_blank">Userid/Email</a>:</font></td>
          <td valign='top'><small>Userid used to log in on the Network Startup Resource Center Network Education Calendar system. Your userid <i>is</i> your email address. If you forget your password you can use your userid and password recovery question below to reset your password. If you indicate that you are willing to act as a resource person then it is critical that your userid be a valid email address.  <font size="1"><a href="/<?=ROOT_DIR?>/helpfiles/Privacy_Statement.php" target="_blank">Privacy policy</a></font>
<?php
    if((isset($sessionID)) && (!empty($register_error_array["userid"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $register_error_array["userid"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>

<!-- username cont. -->

        <tr>
          <td width='0'></td>
          <td valign='top'><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='userid1' size='32'  value='".$register_formvalues_array["userid1"]."' maxlength='96'><font size='2'>&nbsp;<i>Enter your userid twice to verify it matches</i>\n";
      echo "<br>\n";
      echo "<input type='text' name='userid2' size='32'  value='".$register_formvalues_array["userid2"]."' maxlength='96'>\n";
    }
    else
      {
	echo "<input type='text' name='userid1' size='32'  value='' maxlength='96'><font size='2'>&nbsp;<i>Enter your userid twice to verify it matches</i>\n";
	echo "<br>\n";
	echo "<input type='text' name='userid2' size='32'  value='' maxlength='96'>\n";
      }

?>

</font></td>
        </tr>

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
    if((isset($sessionID)) && (!empty($register_error_array["first_name"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $register_error_array["first_name"];
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
      echo "<input type='text' name='first_name' size='44'  value='".$register_formvalues_array["first_name"]."' maxlength='254'>\n";
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
          <td width='15%' valign='top'><font size='2'>

* <a href="/<?=ROOT_DIR?>/helpfiles/Register_lastname.php" target="_blank">Last Name(s)</a>:</font></td>
<td><small>Your last name. This will be used for materials you submit or update. Both your first and last name(s) will be displayed.

<?php
    if((isset($sessionID)) && (!empty($register_error_array["last_name"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $register_error_array["last_name"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>
        <tr>
          <td width='0 valign='top'></td>
          <td><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='last_name' size='44'  value='".$register_formvalues_array["last_name"]."' maxlength='254'>\n";
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
          <td><small>Choose a password and type it twice; please use at least six(6) characters.</small>

<?php
    if((isset($sessionID)) && (!empty($register_error_array["password"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $register_error_array["password"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>
        <tr>
          <td width='0'></td>
          <td><font size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='password' name='password1' size='20'  value='".$register_formvalues_array["password1"]."'></font><br><font size='2'>\n";
      echo "<input type='password' name='password2' size='20'  value='".$register_formvalues_array["password2"]."'>\n";
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
          forget your password. Then, enter the answer in the second box below. Your answer is not case sensitive, but spaces and punctuation are included.</font>
<?php
    if((isset($sessionID)) && (!empty($register_error_array["pw_recover"])))
          {
	echo "<br><b><font color='#ff0000'>\n";
	echo $register_error_array["pw_recover"];
	echo "</b></font>\n";
	      }
?>

</small></td>
        </tr>

        <tr>
          <td width='0'></td>
          <td valign='top'><font face='Arial' size='2'>

<?php
    // If we are reloading the page, then show what was in the form to begin with.
    if (isset($sessionID)) {
      echo "<input type='text' name='pw_recover_question' size='32'  value='".$register_formvalues_array["pw_recover_question"]."' maxlength='96'> <font size='1'><b>Question</b> (Example: 'My favorite food')</font>\n";
      echo "<br>\n";
      echo "<input type='text' name='pw_recover_answer' size='32'  value='".$register_formvalues_array["pw_recover_answer"]."' maxlength='96'> <font size='1'><b>Answer</b> (Example: 'Oysters')</font>\n";
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

<!-- End of form items -->

<br />

<table border="0" cellpadding="0" cellspacing="0" width="742">
<tr><td align="left">
<hr>
<input type='submit' value='SUBMIT' name='SUBMIT'>
</td></tr>
<tr><td align="left">
<br />
<b class="bold">Privacy Notice:</b> We do not display user email addresses on static pages or to anyone who is not a registered user logged into this site. In addition, we do not share, sell or make available your information to outside parties.
</td></tr>
</table>

</td></tr>
</table>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    //$our_filename = "/var/www/calendar/scripts/register.php";
    //include '/var/www/calendar/include/footer-old.php';
    include FILE_PATH . "/include/footer.php";
?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

} // end function signup_page








function valid_signup($authedUser,
                      $sessionID,
                      $register_formvalues_array,
                      $register_error_array)

{


// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - Verification of Data";
$header_heading = "User Registration";
$header_referrer = "/" . ROOT_DIR . "/scripts/register.php";
include FILE_PATH . "/include/header.php";

if($DEBUG) {
  print "<br />*****************************<br />";
  echo("\n\r".FILE_PATH."\n\r");
  print "<br />*****************************<br />";
  print_r($_SERVER);
  print "<br />*****************************<br />";
  print_r($_POST);
  print "<br />*****************************<br />";
  print_r($_SESSION);
  print "<br />*****************************<br />";
  print_r($register_formvalues_array);
  print "<br />*****************************<br />";
  print "<br />*****************************<br />";
}
?>


<tr><td>
<br>
<b class="title">Verify that your user information is correct</b>
<p>
   If it is, press the <strong>REGISTER</strong> button.
<br>
   If it is <em>not</em>, press the <b>BACK</b> button to make corrections.
<br>
<div align='center'>
<strong>Note: upon pressing 'REGISTER' you will be redirected to the home page, and you will be logged in.</strong>
</div>
<hr />
<strong>Required Information</strong>
<br />&nbsp;
</td></tr>
<tr><td>

<?php
echo"<form method='POST' action='/".ROOT_DIR."/scripts/register.php'>\n";
?>

<table border="0" cellpadding="0" cellspacing="0" width="742">

<!-- userid -->

<tr>
<td width='25%' valign='top'><font size='2'>* Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$register_formvalues_array['userid1']. "</font></b>\n";
?>
</td></tr>

<!-- password recovery question-->

<tr>
<td width='25%' valign='top'><font size='2'>* Password recovery question:</td>
<td width='2'></td valign='top'>
<td valign='top'><font face='Arial' size='2'>

<?php
echo "<font color='#000000'>Not displayed for security reasons.</font>\n";
//echo "<font color='#0000ff'><b>" .$register_formvalues_array['pw_recover_question']. "</font></b>\n";
?>
</td></tr>

<!-- password recovery answer -->

<tr>
<td width='25%' valign='top'><font size='2'>* Password recovery answer:</td>
<td width='2'></td valign='top'>
<td valign='top'><font face='Arial' size='2'>

<?php
echo "<font color='#000000'>Not displayed for security reasons.</font>\n";
//echo "<font color='#0000ff'><b>" .$register_formvalues_array['pw_recover_answer']. "</font></b>\n";
?>
</td></tr>

<!-- firstname -->

<tr>
<td width='25%' valign='top'><font size='2'>* First Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$register_formvalues_array['first_name']. "</font></b>\n";
?>
</td></tr>


<!-- lastname -->

<tr>
<td width='25%' valign='top'><font size='2'>* Last Name:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#0000ff'><b>" .$register_formvalues_array['last_name']. "</font></b>\n";
?>
</td></tr>

<!-- password -->

<tr>
<td width='25%' valign='top'><font size='2'>* Password:</td>
<td width='2'></td>
<td><font face='Arial' size='2'>

<?php
echo "<font color='#000000'>Not displayed for security reasons.</font>\n";
?>
</td></tr>
</table>

<tr><td align="left">
<hr />
    <input type='submit' value='REGISTER' name='SUBMIT'>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <input type='submit' value='BACK' name='SUBMIT'>
</td></tr>
</table>

</form>
<br>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    //$our_filename = "/var/www/calendar/scripts/register.php";
    include FILE_PATH . '/include/footer-old.php';
?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

} // end function valid_signup







// Function create_user:
//
// A few things to note:
// - This only creates standard users. Or, that is, folks with privilege
//    level 0.
// - For now we'll leave serial to the default of '0'
// - Enter date ts (timestamp) are the same at first.
// - Nothing will be entered for enter_user or update_user when we touch
//   this record via this interface.
// - ** Issue: How to create ID **
//

function create_user($sessionID, $register_formvalues_array)
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
| available                         | enum('Y','N')       | YES  |     | N                   |                |
| language1                         | varchar(255)        | YES  |     | NULL                |                |
| language2                         | varchar(255)        | YES  |     | NULL                |                |
| language3                         | varchar(255)        | YES  |     | NULL                |                |
| country                           | char(2)             | YES  |     | NULL                |                |
| region                            | char(3)             | YES  |     | NULL                |                |
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

 $md5_password = "MD5:" . strtoupper(md5($register_formvalues_array["password1"]));
 $privilege = 0;

 $creation_date = date("Y-m-d H:i:s");

 //
 // As in pasword, same goes for userid.
 //

 $creation_user = $register_formvalues_array["userid1"];
 $update_user =  $register_formvalues_array["userid1"];

 $username = $register_formvalues_array["first_name"]. " " .$register_formvalues_array["last_name"];

 // Now it's time to insert our new contact record in to the database.
 // Look at mysql_insert_id as a way to create the new id for us instead.

 $result = db_insert("INSERT INTO user
   ( userid
   , password
   , pw_recover_question
   , pw_recover_answer
   , first_name
   , last_name
   , name
   , creation_date
   , creation_user
   , update_user
   ) values (?,?,?,?,?,?,?,?,?,?)",
   array(
     $register_formvalues_array["userid1"]
   , $md5_password
   , $register_formvalues_array["pw_recover_question"]
   , $register_formvalues_array["pw_recover_answer"]
   , $register_formvalues_array["first_name"]
   , $register_formvalues_array["last_name"]
   , $username
   , $creation_date
   , $creation_user
   , $update_user
   ));

//
// User creation success!
//

 if($result[0])
   {
   // session_register("authenticated_user");
   // global $authenticated_user;
   $authenticated_user = $register_formvalues_array["userid1"];
   $_SESSION['authenticated_user'] = $authenticated_user;


	//
	// Send an email to the system administrator letting them know o fth enew user
	// registration. Too many spammers out there so we need to be keeping track of
	// this. Note - It might be a good idea to move "calendar@nsrc.org" to a site-wide
	// variable at some point.
	//

	$first_name = $register_formvalues_array["first_name"];
	$last_name = $register_formvalues_array["last_name"];
	$new_userid = $register_formvalues_array["userid1"];

	if($register_formvalues_array["available_button"] == 'Y')
	    {
$message = "\r\n" .$first_name. " " .$last_name. " has registered as " .$new_userid.

"\r\n\r\n------------------------------------------ \r\n\r\n";
	    }
	else
	    {
$message = "\r\n" . $first_name. " " .$last_name. " has registered as " .$new_userid.
"\r\n\r\nThey did not indicate they were available to teach.";
"\r\n\r\n------------------------------------------ \r\n\r\n";
	    }

	$isoc_admin = "calendar@nsrc.org";
	$subject = "New Network Startup Resource Center Network Education Calendar user registration: " .$new_userid;
 	$sender = "calendar@nsrc.org";

//
// We should deal with this if mail fails, but at this point registration has worked, so the
// user is in...
//

     	$result = mail($isoc_admin,
                    $subject,
                    $message,
                    "From: $sender\r\n");


  // Go back to main page, but leave ssl connection

   header("Location: https://".$_SERVER['SERVER_NAME']. "/" . ROOT_DIR . "/index.php");

   }
     else
       {
?>
	 <html>
         <head>
         <title>Error during User Create</title>
         </head>
         <body>
         <center><b>Error creating your user record. Please contact calendar@nsrc.org for help.</b></center>
         <p>
	 The error that occured was:
         <center>
<?php
      echo $result[1] . "</center>\n";
?>
          <p>
          Return to Network Startup Resource Center Network Education Calendar <a href="http://nsrc.org/<?=ROOT_DIR?>/index.php">Home Page</a></p>
          </body>
          </html>
<?php
       }

 } // End function create_contact



// Main

// Flow of events is like this:
//   - Display the default provider page...
//
// To Do:
// - Fix the validate email script to catch 'user@dom.'
// - Once all vars are fine, then create function to redisplay to the screen what
//   has been entered. If the user likes this, then we'll let them apply, otherwise,
//   if they don't, then they go back to the screen with values filled in.
// - Fix session checking code.
// - Work on ereg/pereg regular expression code to check for valid
//   characters in strings below (i.e. '&' in a userid?, etc.). Warn
//   users if the string is not valid.


// Possible flow...
// First time to the page, just display it - default.
// Submitted page, Submit = cancel, go back...
// Submitted page, we are looking for Submit = 'next', if so, check for
// errors - if none, display valid page...
//          if error, redisplay the page
// if Submit = Apply (I think) we create the record
// If Submit = Back, then we redisplay what we have in the formvalues array
//             (probably should check for values...)
//
// First time entering the page. No other buttons pressed.

//echo $SUBMIT;

/*
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
*/


//
// You can't be logged in to create a new user account. Bounce the user.
//

//if(session_is_registered("authenticated_user"))
if (isset($_SESSION["authenticated_user"]))
{
  header("Location: ../scripts/errors.php?id=1");
}

if (($SUBMIT !='SUBMIT') &&
    ($SUBMIT !='REGISTER') &&
    ($SUBMIT !='BACK') &&
    (!isset($_SESSION["authenticated_user"])))
{
 signup_page('',
	      '',
	      '',
	      '');
}

// User filled out the form and now wants to go to the next step.

elseif ($SUBMIT == 'SUBMIT')
{
  $register_formvalues_array = array('userid1' => htmlspecialchars($_POST["userid1"])
			   , 'userid2' => htmlspecialchars($_POST["userid2"])
			   , 'first_name' => htmlspecialchars($_POST["first_name"])
			   , 'last_name' => htmlspecialchars($_POST["last_name"])
			   , 'password1' => htmlspecialchars($_POST["password1"])
			   , 'password2' => htmlspecialchars($_POST["password2"])
			   , 'pw_recover_question' => htmlspecialchars($_POST["pw_recover_question"])
			   , 'pw_recover_answer' => htmlspecialchars($_POST["pw_recover_answer"])
			   , 'available_button' => htmlspecialchars($_POST["available_button"])
			   , 9);

  // *** This is critical! ***
  // If you _don't_ make this global, and you _don't_ register this
  // array with the session, then you cannot go back and redisplay
  // the contents of what the user filled out at a later time.

  global $register_formvalues_array;
  //  session_register("register_formvalues_array");
$_SESSION["register_formvalues_array"] = $register_formvalues_array;

  // Technically we probably don't need our error array to be global, but
  // we might at a later time, so it's declared globally here, even though
  // it's passed to the function.

  $register_error_array = array('count' => '0'
		       , 'userid' => ''
		       , 'first_name'  => ''
		       , 'last_name'  => ''
		       // There are two password form fields. This is set if they
		       // don't match.
		       , 'password' => ''
		       , 'pw_recover' => ''
		       , 'available_button' => ''
		       , 7);

  global $register_error_array;
 // session_register("register_error_array");
$_SESSION["register_formvalues_array"] = $register_formvalues_array;


    //
    // We could check for error in one function, but the original checkforerrors
    // function we written before we added the available button functionality.
    //
    // It's much easier to deal with these as truly separate cases. Note that
    // both functions are in the file /include/checkuser.php
    //

  $register_error_array = checkforerrors($register_error_array, $register_formvalues_array);

  // Form was filled out, but there are errors. Redisplay the form. We'll
  // check on $register_error_array["count"] for errors and then display them,
  // plus we'll fill in the fields from $register_formvalues_array.

  if($register_error_array["count"] > 0)
    {
      signup_page($authenticatedUser,
		  $PHPSESSID,
		  $register_error_array,
		  $register_formvalues_array);
    }

  // There are no errors, display what they entered for confirmation.

  if($register_error_array["count"] == 0)
    {
     valid_signup($authenticatedUser,
	          $PHPSESSID,
		  $register_formvalues_array,
                  $register_error_array);
    }
// User wants to go back and make some changes to entered information.
}
elseif($SUBMIT == 'BACK')
    {

      signup_page($authenticatedUser,
		  $PHPSESSID,
		  $register_error_array,
		  $register_formvalues_array);
    }

// User likes what they see and is ready to create their record.

elseif($SUBMIT == 'REGISTER')
{
      create_user($PHPSESSID,
		  $register_formvalues_array);
}
else
{
    // I don't know why we'd be here, but if we hit an unexpected situation
    // (like we have a global variable conflict that confuses the flow
    // structure) give the user some sort of response.
    //

  echo "<b>SUBMIT =: </b>" .$SUBMIT. "<br>\n";

  echo "<html><head><title>Unknown Problem</title</head><body>\n";
  echo "<h2>Network Startup Resource Center Network Education Calendar User Signup</h2>\n";
  echo "<h2>An unknown error was encountered.</h2>\n";
  echo "You can return to the main page <a href='/" . ROOT_DIR . "/index.php'>here</a>, or contact <a href='mailto:calendar@nsrc.org'>calendar@nsrc.org</a> and let us know you encountered this error message when trying to register. We will try to resolve the problem as quickly as possible.\n";
  echo "</body></html>\n";
}
?>
