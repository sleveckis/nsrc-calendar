<?php
ob_start();
session_start();
$session = session_id();

//$FILEPATH =  realpath(dirname(__FILE__));
//$FILEPATH = "/var/www/calendar";

include "../config.php";

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

// Emulate register_globals on ************************************ KN
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





function login_page($authed_user,
		    $user_name,
		    $logged_in,
		    $requested_page,
		    $formvalues_login_array,
		    $formvalues_login_error_array)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - Login Required";
$header_heading = "Login Required";
$header_referrer = "/" . ROOT_DIR . "/scripts/login-required.php";
include FILE_PATH . "/include/header.php";

//
// We have the following states to deal with:
//
// 1.) User is logged in and has come here.
// 2.) User is not logged in and has come here with no referring page.
// 3.) User tried to log in but their username is incorrect.
// 4.) User tried to log in but their password is incorrect.
//

echo "<tr><td class='content'>\n";


if ($logged_in == 'TRUE')
   {
?>

<table width="742" border="0" cellspacing="1" cellpadding="0">
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" width="740" bgcolor="#e3e3e3">
<tr><td>
<strong>Already logged in</strong>: You are already logged in to the Network Startup Resource Center Network Education Calendar. If you wish to log out and log in as a different user then press

<?php
            echo "<a href='/" . ROOT_DIR . "/scripts/login-required.php?logout=TRUE'>logout</a>.\n";
?>

You will be redirected to this page to log in. Once you log in as a different user you will be redirected to the <a href='/index.php'>home page</a> of this site.
</td></tr>
</table>
</td></tr>
</table>

<?php

    } // end if logged_in is true

elseif($logged_in == 'FALSE')
    {
?>

<table width="742" border="0" cellspacing="1" cellpadding="0">
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" width="740" bgcolor="#e3e3e3">
<strong>Login Required</strong>
<p>
Apologies for the inconvenience, but you must be logged in to this site to access this resource. You can log in at this time and then you will be redirected to the page you were originally trying to access. If you do not have an account on this site you can <a href="/<?=ROOT_DIR?>/scripts/register.php">register here</a>.

<?php
    if($requested_page == '')
        {
            ?>
            <p>
            <strong>No referring page:</strong> It appears that you did not click a link from another page on this site requiring login. This means that once you successfully log in you will be directed to the <a href='/index.php'>home page</a> of this site.
            </font></font>
            <?php
        }
?>

</td></tr>
</table>
</td></tr>
</table>

<?php

    } // end if logged in FALSE

    //
    // Warn user if not using ssl (https) port to log in.
    //

    if($_SERVER['SERVER_PORT'] != 443)
        {
            ?>
            <strong><font color="#ff0000">Security Warning:</font></strong> This login page is insecure
            Use secure login page available <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/<?=ROOT_DIR?>/scripts/login-required.php?requested_page=<?php echo $requested_page?>">here</a>.
            <?php
        }

echo "<p>\n";

//
// Determine if we are submitting this form with or without $referrer set.
//

if(!empty($requested_page))
    {
        echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/login-required.php?requested_page=".$requested_page."'>\n";
    }
else
    {
        echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/login-required.php'>\n";
    }


if ($logged_in == 'FALSE')
    {

?>

        <table width="742" bgcolor="#ffffff">
        <tr><td width="60" valign="middle" align="left">

        <?php
            if(!empty($formvalues_login_error_array['authed_user']))
            {
              echo "<b class='bold'>Username:</b>\n";
            }
            else
            {
              echo "<b class='bold'>Username:</b>\n";
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
        <td width="510">

        <?php
            if(!empty($formvalues_login_error_array['authed_user']))
            {
              echo "<font color='#ff0000'>" .$formvalues_login_error_array['authed_user']. "</font>\n";
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
		    echo "<font color='#ff0000'><strong>Password</font>:</strong>\n";
            }
            else
            {
              echo "<strong>Password:</strong>\n";
            }
        ?>

        </td>
        <td align="left" width="100" valign="middle">
        <input type='password' size='15' value='' name='authed_user_pw'>
        </td>
        <td width="510">

        <?php
            if(!empty($formvalues_login_error_array['authed_user_pw']))
            {
              echo "<font color='#ff0000'>" .$formvalues_login_error_array['authed_user_pw']. "</font>Recover your password <a href='/" . ROOT_DIR . "/scripts/recover-password.php?referrer=/calendar/scripts/login.php'>here</a>.\n";
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
        <td width="510">
           &nbsp;</td>
        </tr>
        </table>

        </form>

<?php
    } // end if logged_in == 'FALSE'

echo "<br>\n";

 echo "</td></tr>\n";
 echo "</table>\n";

// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    include FILE_PATH . "/include/footer.php";

?>

</body>
</html>

<?php
// End login-required.php page, thus you need the </body> and </html>
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

$requested_page = htmlspecialchars($_GET["requested_page"]);

if ($_SERVER["HTTPS"] != 'on')
    {
        header("Location: https://" .$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?requested_page=$referrer");
    }


if ($logout == TRUE)
    {
        $local_referrer = $requested_page;
        session_destroy();
        if(!empty($local_referrer))
            {
                header("Location: /" . ROOT_DIR . "/scripts/login-required.php?requested_page=$local_referrer");
            }
        else
            {
                header("Location: /" . ROOT_DIR . "/scripts/login-required.php");
            }
    }

// User is already logged in. Display the page appropriately.

//elseif(session_is_registered("authenticated_user"))
elseif (isset($_SESSION["authenticated_user"]))
    {
        $authed_user = $_SESSION["authenticated_user"];
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
                   $requested_page,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
    }

// User has just arrived, is not logged in.

//elseif((!session_is_registered("authenticated_user")) and
elseif ((!isset($_SESSION["authenticated_user"])) and
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
                   $requested_page,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
    }

// User has pressed Submit to log in.

//elseif((!session_is_registered("authenticated_user")) and
elseif ((!isset($_SESSION["authenticated_user"])) and
       ($SUBMIT == 'Login'))
    {
        $logged_in = 'FALSE';

        $formvalues_login_array = array('authed_user' => htmlspecialchars($_POST['authed_user']),
                                        'authed_user_pw' => htmlspecialchars($_POST['authed_user_pw']),
                                        2);

        $formvalues_login_error_array = authenticate($formvalues_login_array['authed_user'],
                                                     $formvalues_login_array['authed_user_pw']);


        if(($formvalues_login_error_array["authed_user"] == '') and
           ($formvalues_login_error_array["authed_user_pw"] == ''))
          {
            $authenticated_user = $authed_user;
	    $_SESSION["authenticated_user"] = $authenticated_user;
            $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
            $user_name = $row_name['name'];

            if($requested_page != '')
              {
		//echo "<br>requested_page = ".$local_requested."\n";
                header("Location: http://".$_SERVER['SERVER_NAME'].$requested_page);
              }
            else
              {
		//echo "<br>requested_page = ".$local_requested."\n";
                header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");
              }

          } // end if
        else
          {
                login_page($authed_user,
                   $user_name,
                   $logged_in,
                   $requested_page,
                   $formvalues_login_array,
                   $formvalues_login_error_array);
          } // end else
    }
else
 {
   echo "<html><body><title='Network Startup Resource Center Network Education Calendar: Unexpected Error'></title>\n";
   echo "<b>Unexpected error. Please contact calendar@nsrc.org for help.</b>\n";
   echo "</body></html>\n";
 }

// end file
?>
