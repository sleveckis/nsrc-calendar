<?php
ob_start();
session_start();
$session = session_id();

// define FILE_PATH
include "../config.php";

//$FILEPATH =  realpath(dirname(__FILE__));
//$FILEPATH = "/var/www/calendar";


// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Used to validate email entered in the form. Long function.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkemail.php";
include FILE_PATH . "/include/checkemail.php";

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as
// subroutines. Eventually most of the code in main should go here.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkuser.php";
include FILE_PATH . "/include/checkuser.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

function login_page($authed_user,
		    $user_name,
		    $logged_in,
		    $referring_page,
		    $formvalues_login_array,
		    $formvalues_login_error_array)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - User Login";
$header_heading = "User Login";
include FILE_PATH . "/include/header.php";

//
// We have the following states to deal with:
//
// 1.) User is logged in and has come here.
// 2.) User is not logged in and has come here with no referring page.
// 3.) User tried to log in but their username is incorrect.
// 4.) User tried to log in but their password is incorrect.
//

?>
<tr><td>

<?php

if ($logged_in == 'TRUE')
   {
?>

<b class="bold">>Already logged in</b>: You are already logged in to the Network Startup Resource Center Network Education Calendar. If you wish to log out and log in as a different user then press

<?php
    if(!empty($referring_page))
        {
            echo "<a href='/" . ROOT_DIR . "/scripts/login.php?logout=TRUE&referrer=$referring_page'>logout</a>.\n";
        }
    else
        {
            echo "<a href='/" . ROOT_DIR . "/scripts/login.php?logout=TRUE'>logout</a>.\n";
        }
?>

You will be redirected to this page to log in. Once you log in as a different user you will be redirected to the <a href=FILE_PATH . "/index.php">calendar home page</a>.

</td></tr>
</table>

<?php

    } // end if logged_in is true

elseif($logged_in == 'FALSE')
    {
?>

<tr><td>
<b class="bold">Login</b>: Please enter in your username and password. This will allow you to access additional information on this site, upload or update materials, and change your user profile. If you do not have an account for this site you can <a href="/<?=ROOT_DIR?>/scripts/register.php">register here</a>.

<?php
    if($referring_page == '')
        {
            ?>
            <p>
            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
            <font color="#ff0000"><b>No referring page:</b></font> It appears that you did not click a "login" link from another page on this site. This means that once you successfully log in you will be directed to the <a href='/<?=ROOT_DIR?>/index.php'>calendar home page</a>.
            </font></font>
            <?php
        }
    else
        {
            ?>
            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
            <p>
            Once you successfully log in you will be directed back to the page where you clicked "login".
            </font></font>
            <?php
        }
?>

<br />
<br />

<?php

    } // end if logged in FALSE

    //
    // Warn userif not using ssl (https) port to log in.
    //

    if($_SERVER['SERVER_PORT'] != 443)
        {
            ?>
            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
            <font color="#ff0000"><b>Security Warning:</b></font> This login page is insecure
            Use secure login page available <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/<?=ROOT_DIR?>/scripts/login.php?referrer=<?php echo $referring_page?>">here</a>.
            </font></font>
            <?php
        }

echo "<p>\n";

//
// Determine if we are submitting this form with or without $referrer set.
//

if(!empty($referring_page))
    {
        echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/login.php?referrer=".$referring_page."'>\n";
    }
else
    {
        echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/login.php'>\n";
    }


if ($logged_in == 'FALSE')
    {

?>

        <table width="500" bgcolor="#ffffff">
        <tr><td width="60" valign="middle" align="left">

        <?php
            if(!empty($formvalues_login_error_array['authed_user']))
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "<font color='#ff0000'><b>Username</font>:&nbsp;</b>\n";
              echo "</font>\n";
            }
            else
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "<b>Username:&nbsp;</b>\n";
              echo "</font>\n";
            }
        ?>

        </td>
        <td align="left" width="100" valign="middle">

        <?php

            if(!empty($formvalues_login_error_array['authed_user_pw']))
                {
                    $un = $formvalues_login_array['authed_user'];
                    echo "<input selected type='text' size='15' value='$un' name='authed_user'>\n";
                }
            else
                {
                    echo "<input selected type='text' size='15' value='' name='authed_user'>\n";
                }
        ?>
        </td>
        <td width="340">

        <?php
            if(!empty($formvalues_login_error_array['authed_user']))
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "&nbsp;<font color='#ff0000'>" .$formvalues_login_error_array['authed_user']. "</font>\n";
              echo "</font>\n";
            }
            else
            {
              echo "&nbsp;\n";
            }
        ?>

        </td></tr>
        <tr><td width="60" valign="middle" align="left">

        <?php
            if(!empty($formvalues_login_error_array['authed_user_pw']))
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "<font color='#ff0000'><b>Password</font>:&nbsp;</b>\n";
              echo "</font>\n";
            }
            else
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "<b>Password:&nbsp;</b>\n";
              echo "</font>\n";
            }
        ?>

        </td>
        <td align="left" width="100" valign="middle">
        <input type='password' size='15' value='' name='authed_user_pw'>
        </td>
        <td width="340">

        <?php
            if(!empty($formvalues_login_error_array['authed_user_pw']))
            {
              echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
              echo "&nbsp;<font color='#ff0000'>" .$formvalues_login_error_array['authed_user_pw']. "</font>Recover your password <a href='/" . ROOT_DIR . "/scripts/recover-password.php?referrer=/" . ROOT_DIR . "/scripts/login.php'>here</a>.\n";
              echo "</font>\n";
            }
            else
            {
              echo "&nbsp;\n";
            }
        ?>


        </td></tr>
        <tr><td width="60" valign="middle" align="left">
            &nbsp;
        </td>
        <td align="left" width="100" valign="middle">
        <input type='submit' value='Login' name='SUBMIT'>
        &nbsp;&nbsp;
        <font size="1"><a href="/<?=ROOT_DIR?>/scripts/register.php"><font color="#ff0000">Register</font></a>
        </td>
        <td width="340">
           &nbsp;</td>
        </tr>
        </table>

        </form>

<?php
    } // end if logged_in == 'FALSE'

?>
</td></tr>
</table>

<br />

<?php


// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    	//$our_filename = "/var/www/calendar/scripts/login.php";
    	include FILE_PATH . "/include/footer.php";

?>

</body>
</html>

<?php
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

    } // end function login_page
?>



<?php
//
// Main
//

//
// We have the following states to deal with:
//
// 0.) User is here, but not in secure mode. Force them to use ssl.
// 1.) User is logged in and has come here.
// 2.) User is not logged in and has come here with no referring page.
// 3.) User tried to log in but their username is incorrect.
// 4.) User tried to log in but their password is incorrect.
//

//
// Spoof "register_globals on in php.ini as it's deprecated past php 5.4.
//
// Emulate register_globals on
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


if (isset($_GET["referrer"]))
	{
		$referring_page = htmlspecialchars($_GET["referrer"]);
	}
else
	{
		$referring_page = "";
	}

$referrer = $referring_page;

if (isset($_GET["logout"]))
	{
		$logout = htmlspecialchars($_GET["logout"]);
	}
else
	{
		$logout = "";
	}

// if ($_SERVER["HTTPS"] != 'on')
//     {
//         header("Location: https://" .$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?referrer=$referrer");
//     }
// this is debug code to check for authenticated user
echo $SUBMIT;

//print_r($_POST);

// this is the end of debug code
if ($logout == TRUE)
    {
        $local_referrer = $referrer;
        session_destroy();
        if(!empty($local_referrer))
            {
                header("Location: /" . ROOT_DIR . "/scripts/login.php?referrer=$local_referrer");
            }
        else
            {
                header("Location: /" . ROOT_DIR . "/scripts/login.php");
            }
    }

// User is already logged in. Display the page appropriately.

//elseif(session_is_registered("authenticated_user"))
elseif (isset($_SESSION["authenticated_user"]))
    {
        $authed_user = htmlspecialchars($_POST["authenticated_user"]);
        $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
        $user_name = $row_name['name'];

        $formvalues_login_array = array('authed_user' => '',
                                        'authed_user_pw' => '',
                                        2);

        $formvalues_login_error_array = array('authed_user' => '',
                                              'authed_user_pw' => '',
                                               2);

        $logged_in = 'TRUE';

        login_page($authed_user,
                   $user_name,
                   $logged_in,
                   $referrer,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
    }

// User has just arrived, is not logged in.

//elseif((!session_is_registered("authenticated_user")) and
elseif (!isset($_SESSION["authenticated_user"]) and
       ($SUBMIT != 'Login'))
    {
        $logged_in = 'FALSE';

        $formvalues_login_array = array('authed_user' => '',
                                        'authed_user_pw' => '',
                                        2);

        $formvalues_login_error_array = array('authed_user' => '',
                                              'authed_user_pw' => '',
                                               2);

                login_page($authed_user,
                   $user_name,
                   $logged_in,
                   $referrer,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
    }

// User has pressed Submit to log in.

//elseif((!session_is_registered("authenticated_user")) and
elseif (!isset($_SESSION["authenticated_user"]) and
       ($SUBMIT == 'Login'))
    {
// this is more debug code
echo "attemping login";
echo $SUBMIT;
// end debug code
        $logged_in = 'FALSE';

        $formvalues_login_array = array('authed_user' => htmlspecialchars($_POST['authed_user']),
                                        'authed_user_pw' => htmlspecialchars($_POST['authed_user_pw']),
                                        2);

        $formvalues_login_error_array = authenticate($formvalues_login_array['authed_user'],
                                                     $formvalues_login_array['authed_user_pw']);


        if(($formvalues_login_error_array["authed_user"] == '') and
           ($formvalues_login_error_array["authed_user_pw"] == ''))
          {
            //session_register("authenticated_user");
	   // $_SESSION["authenticated_user"];
            global $authenticated_user;
            $authenticated_user = $authed_user;
	    $_SESSION["authenticated_user"] = $authenticated_user;
            $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
            $user_name = $row_name['name'];

            if($referrer != '')
              {
                header("Location: http://".$_SERVER['SERVER_NAME'].$referrer);
              }
            else
              {
                header("Location: http://".$_SERVER['SERVER_NAME']."/" . ROOT_DIR . "/index.php");
              }

          } // end if
        else
          {
                login_page($authed_user,
                   $user_name,
                   $logged_in,
                   $referrer,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
          } // end else
    }
else
 {
   echo "<html><body><title='NSRC Network Education calendar: Unexpected Error'></title>\n";
   echo "<b>Unexpected error. Please contact nsrc@nsrc.org for help.</b>\n";
   echo "</body></html>\n";
 }

// end file
?>
