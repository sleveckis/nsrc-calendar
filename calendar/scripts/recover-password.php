<?php
ob_start();
session_start();
$session = session_id();

// recover-password.php
//
// Hervey Allen for ISOC, October 2003
//

// define FILE_PATH
include "../config.php";

//$FILEPATH =  realpath(dirname(__FILE__));
//$FILEPATH = "/var/www/calendar";


// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

// Emulate register_globals on ************************************* KN
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
// END emulate register globals ********************************** KN


//
// For now, we turn off warnings
//

error_reporting(E_ERROR);

//
// Function pw_recover1
//

function pw_recover1($pw_recover_formvalues_array,
                     $pw_recover_error_array,
                     $referrer)

{

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.
$header_title = "Network Startup Resource Center Network Education Calendar - Password Recovery";
$header_heading = "Password Recovery/Reset";
$header_referrer = FILE_PATH . "/scripts/recover-password.php";
include FILE_PATH . "/include/header.php";

?>    
<tr><td class="content">

        <table width="742" border="0" cellspacing="1" cellpadding="0">
        <tr><td>
        <table border="0" cellpadding="2" cellspacing="0" width="740" bgcolor="#e3e3e3">
        <tr><td>
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <strong>Password Recovery/Reset:</strong>
        <p>
        If you have forgotten your password, then you can use this form to reset your password to something different. You will be asked for your userid (your email address when you registered). Based on your userid we will then display the original "Password Recovery Question" you entered when you first registered. You enter in the answer to this as well as a new password. If your answer was correct, then the password on your account will be reset.
        <p>
        If you cannot remember your original userid or the answer to your original "Password Recovery Question" then please contact this site's administrator for help regaining access to your account. Send email to <font color="#0000ff">calendar@nsrc.org</font>.
        </font></font>
        </td></tr>
        </table>
        
        </td></tr>
        </table>

<p>

<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<strong>Enter in your userid to start the password reset process:</strong>
</font></font>

<?php
echo "<br>\n";

//echo "<form method ='POST' action = '" .$_SERVER['PHP_SELF']. "?referrer='$refererr"'>\n";
  echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/recover-password.php?referrer=".$referrer."'>\n";


    if(!empty($pw_recover_error_array['userid']))
        {
            echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
            echo "<b><font color='#ff0000'>" .$pw_recover_error_array['userid']. "</b><br>\n";
            echo "</font></font></font>\n";
        }


echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";

    if(!empty($pw_recover_formvalues_array['userid']))
        {
        echo "<input type='submit' value='Submit Userid' name='SUBMIT'>&nbsp;&nbsp;<input type='text' name='userid' size='25'  value='" .$pw_recover_formvalues_array['userid']. "' maxlength='254'>&nbsp;<font color='#0000ff'>(<i>Your userid should be a valid email address</i>)</font\n";
        }
    else
        {
        echo "<input type='submit' value='Submit Userid' name='SUBMIT'>&nbsp;&nbsp;<input type='text' name='userid' size='25'  value='' maxlength='254'>&nbsp;<font color='#0000ff'>(<i>Your userid should be a valid email address</i>)</font>\n";
        }

echo "</font></font>\n";

    //
    // Warn userif not using ssl (https) port to log in.
    // 
    
    if($_SERVER['SERVER_PORT'] != 443)
        {
            ?>
            <br>
            <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
            <font color="#ff0000"><b>Security Warning:</b></font> This page is insecure
            Use secure password recovery page available <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/<?=ROOT_DIR?>/scripts/recover-password.php?referrer=<?php echo $referrer?>">here</a>.
            </font></font>
            <?php
        }  


echo "<p>\n";
echo "</form>\n";

echo "</td></tr>\n";
echo "</table>\n";

// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

    //$our_filename = "/var/www/calendar/scripts/recover-password.php";
    include FILE_PATH . "/include/footer.php";
?>

</body>
</html>

<?php 
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects 
// dependent on your web server and subsequent page order.
} // end function pw_recover1

function pw_recover2($pw_recover_formvalues_array,
                             $pw_recover_error_array)
{

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - Password Recovery";
$header_heading = "Password Recovery/Reset";
$header_referrer = FILE_PATH . "/scripts/recover-password.php";

include FILE_PATH . "/include/header.php";

?>    

<tr><td class="content">

        <table width="742" border="0" cellspacing="1" cellpadding="0">
        <tr><td>
        <table border="0" cellpadding="2" cellspacing="0" width="740" bgcolor="#e3e3e3">
        <tr><td>
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <strong>Password Recovery Question</strong>
        <p>
        Please fill in the answer to the password recovery question below. The answer is not case sensitve, but spaces and punctuation that you may have entered originally are preserved.
        <p>
        After the answer please enter in a password and type it twice; please use at least six(6) characters.
        <p>
        If you cannot remember the answer to your original "Password Recovery Question" then please contact this site's administrator for help regaining access to your account. Send email to <font color="#0000ff">calendar@nsrc.org</font>.
        </font></font>
        </td></tr>
        </table>
        
        </td></tr>
        </table>

<?php

echo "<p>\n";

//echo "<form method ='POST' action = '" .$_SERVER['PHP_SELF']. "'>\n";
echo "<form method='POST' action='/" . ROOT_DIR . "/scripts/recover-password.php?referrer=".$referrer."'>\n";

echo "<table width='500' border='0'>\n";
echo "<tr><td colspan='2'>\n";

echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
echo "<strong>Your Password Recovery Question is:</strong>\n";
echo "<br>\n";

echo "<font color='#0000ff'>\"" .$pw_recover_formvalues_array["question"]. "\"</font>\n";

echo "</td></tr>\n";
echo "<tr><td colspan='2'>\n";

    if(!empty($pw_recover_error_array['answer']))
        {
            echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
            echo "<strong><font color='#ff0000'>" .$pw_recover_error_array['answer']. "</strong><br>\n";
            echo "</font></font></font>\n";
        }
echo "</td></tr>\n";

    if(!empty($pw_recover_formvalues_array['answer']))
        {
        echo "<tr><td width='60'>\n";
        echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
        echo "<strong>Answer: </strong></td><td width='440'><input type='text' name='answer' size='25'  value='" .$pw_recover_formvalues_array['answer']. "' maxlength='254'>&nbsp;<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>(<i>Your answer is not case sensitve</i>)</font></font></font></td></tr>\n";
        }
    else
        {
        echo "<tr><td width='60'>\n";
        echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
        echo "<strong>Answer: </strong></td><td width='440'><input type='text' name='answer' size='25'  value='' axlength='254'>&nbsp;<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>(<i>Your answer is not case sensitive</i>)</font></font></font></td></tr>\n";
        }

echo "<tr><td colspan='2'>\n";
echo "<br>\n";
echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
echo "<strong>Enter in a new password below:</strong>\n";
echo "</font></font>\n";
echo "</td></tr>\n";

echo "<tr><td width='60'>\n";
echo "</td><td width='440'>\n";

    if(!empty($pw_recover_error_array['password1']))
        {
            echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
            echo "<strong><font color='#ff0000'>" .$pw_recover_error_array['password1']. "</strong><br>\n";
            echo "</font></font></font>\n";
        }
echo "</td></tr>\n";
echo "<tr><td width='60'>\n";
echo "&nbsp;\n";
echo "</td><td width='440'>\n";

        if((empty($pw_recover_error_array['password1'])) and
           (!empty($pw_recover_formvalues_array['password1'])))
            {
                echo "<input type='password' name='password1' size='25'  value='" .$pw_recover_formvalues_array['password1']. "'>&nbsp;<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>(<i>Enter new password twice</i>)\n";
            }
         else
            {
                echo "<input type='password' name='password1' size='25'  value=''>&nbsp;<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color='#0000ff'>(<i>Enter new password twice</i>)\n";                
            }

echo "</td></tr>\n";

echo "</td></tr>\n";
echo "<tr><td width='60'>\n";
echo "&nbsp;\n";
echo "</td><td width='440'>\n";
        
        if((empty($pw_recover_error_array['password1'])) and
           (!empty($pw_recover_formvalues_array['password2'])))
            {
                echo "<input type='password' name='password2' size='25'  value='" .$pw_recover_formvalues_array['password2']. "'>\n";
            }
         else
            {
                echo "<input type='password' name='password2' size='25'  value=''>\n";                
            }
echo "</td></tr>\n";

echo "<tr><td colspan='2'>\n";
echo "&nbsp;\n";
echo "</td></tr>\n";

echo "<tr><td width='60'>\n";
echo "&nbsp;\n";
echo "</td><td width='440'>\n";
echo "<input type='submit' value='Submit Information' name='SUBMIT'>\n";

echo "</td></tr>\n";
echo "</table>\n";

echo "<p>\n";
echo "</form>\n";

?>

</td></tr>
</table>
<?php

// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

    //$our_filename = "/var/www/calendar/scripts/recover-password.php";
    include FILE_PATH . "/include/footer-old.php";
?>

</body>
</html>

<?php 
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects 
// dependent on your web server and subsequent page order.

    } // end function pw_recover2



function checkpassword($pw)
{
  // Need to switch to regular expression, but for now let's brute
  // force this! Much slower!
  //  if((eregi("^[_-!@.?$%:]$", $password_text)))
  //  if ((!strstr($pw, '_')) &
  //  (!strstr($pw, '-')) & 
  //  (!strstr($pw, '!')) &
  //  (!strstr($pw, '@')) &
  //  (!strstr($pw, '.')) &
  //  (!strstr($pw, '?')) &
  //  (!strstr($pw, '$')) &
  //  (!strstr($pw, '%')) &
  //  (!strstr($pw, ';')) &
  //  (!strstr($pw, ':')))
  if(!preg_match("/[_\-\!\@\.\?\$\%\;\:]/", $pw))
    {
      return FALSE;
    }
  else
    {
      return TRUE;
    }
} // end function checkpassword.


//
// Check on password and on password recovery answer
//

function pw_recover_error_check($pw_recover_formvalues_array,
                                $pw_recover_error_array)
{
    
 // If either password field is empty tell the user.
 if ((empty($pw_recover_formvalues_array["password1"])) or (empty($pw_recover_formvalues_array["password2"])))
   {
    $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
    $pw_recover_error_array["password1"] = "You have left one of the password fields blank." ;
   }
 // If the passwords are not equal tell the user.
 elseif($pw_recover_formvalues_array["password1"] != $pw_recover_formvalues_array["password2"])
   {
    $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
    $pw_recover_error_array["password1"] = "The passwords do not match! Please re-enter." ;
   }
 // If the password is less than 6 characters long tell the user.
 elseif(strlen($pw_recover_formvalues_array["password1"]) < 6)
   {
    $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
    $pw_recover_error_array["password1"] = "Your password is too short! Your password should be at least 6 characters long." ;
   }
 // If the password does not contain any punctuation values, and it's longer than 
 // 6 characters, then tell the user.
 elseif(!checkpassword($pw_recover_formvalues_array["password1"]))
   {
    $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
    $pw_recover_error_array["password1"] = "Your password does not contain any punctuation. Please add one of the following to your password: '_-!@.?\$%:'." ;
   }
   
    $userid = $pw_recover_formvalues_array["userid"];
    
    $answer_counter = 0;
    $result_answer = db_exec("select * from user where userid= ?", array($userid));
    while ($row_answer = $result_answer->fetch())
        {
            $answer = $row_answer["pw_recover_answer"];
            $answer_counter++;
        }
        
    $user_answer = $pw_recover_formvalues_array["answer"];
    
    if($answer_counter != 1)
        {
            $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
            $pw_recover_error_array["answer"] = "Internal error: unexpected error encountered. Please contact calendar@nsrc.org for help." ;
        }
        
        elseif(empty($answer))
        {
            $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
            $pw_recover_error_array["answer"] = "Error: it appears that no answer text was provided at the time of your registration. Pleae contact calendar@nsrc.org for help." ;  
        }
        
    elseif((strtoupper($answer)) != (strtoupper($user_answer)))
        {
            $pw_recover_error_array["count"] = $pw_recover_error_array["count"] + 1;
            $pw_recover_error_array["answer"] = "Error: your answer \"" .$pw_recover_formvalues_array['answer']. "\" does not match the answer we have on record." ; 
        }
    
return $pw_recover_error_array;
    
} // pw_recover_error_check
//
// Function pw_recover1
//

function pw_recover_success($pw_recover_formvalues_array)

{

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - Password Recovery";
$header_heading = "Password Recovery/Reset";
$header_referrer = ROOT_DIR . "/scripts/recover-password.php";
include FILE_PATH . "/include/header.php";

?>

<tr><td class="content">
    
        <table width="742" border="0" cellspacing="1" cellpadding="0">
        <tr><td>
        <table border="0" cellpadding="2" cellspacing="0" width="740" bgcolor="#e3e3e3">
        <tr><td>
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
        <strong>Password Successfully Reset!</strong>
        <p>
        The password for <?php echo $pw_recover_formvalues_array['userid']?> has been successfully reset. You are now logged in to the Network Startup Resource Center Network Education Calendar.
        </font></font>
        <p>
        
<?php
        if(!empty($pw_recover_formvalues_array["referrer"]))
            {
			    $local_ref = $pw_recover_formvalues_array['referrer'];
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
                echo "Return to the page you started from by clicking <a href='http://nsrc.org$local_ref'>here</a>, or go to the Network Startup Resource Center Network Education Calendar <a href='/" . ROOT_DIR . "/index.php'>main page</a>.\n";
                echo "</font></font>\n";
            }
        else
            {
                echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
                echo "Return to the Network Startup Resource Center Network Education Calendar <a href='/" . ROOT_DIR . "/index.php'>main page</a>.\n";
                echo "</font></font>\n";
            }

?>

        </td></tr>
        </table>
        
        </td></tr>
        </table>

</td></tr>
</table>
<?php

// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

    //$our_filename = "/var/www/calendar/scripts/recover-password.php";
    include FILE_PATH . "/include/footer.php";
?>

</body>
</html>

<?php 
// End instructors page, thus you need the </body> and </html>
// statements or you may get some interesting side affects 
// dependent on your web server and subsequent page order.

    } // end function pw_recover_success





// Main

//
// If we are not secure, force it.
//
$referrer = htmlspecialchars($_GET["referrer"]);

if ($_SERVER["HTTPS"] != 'on')
    { 
        header("Location: https://" .$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?referrer=$referrer");
    }

if ($logout == TRUE)
    {
        session_destroy();    
        header("Location: " . ROOT_DIR . "/scripts/recover-password.php");
    }
    
//
// It doesn't make sense if you are logged in that you are recovering your password...
//
    
//elseif(session_is_registered("authenticated_user"))
if (isset($_SESSION["authenticated_user"]))
    {
        header("Location: " . ROOT_DIR . "/scripts/errors.php?id=10");
    }

elseif($SUBMIT == 'Submit Userid')
    {
        $pw_recover_error_array = array('userid' => ''
                                        , 'answer' => ''
                                        , 'password1' => ''
                                        , 'count' => ''
                                        , 4);
        
        $pw_recover_formvalues_array = array('userid' => htmlspecialchars($_POST["userid"])
                                    , 'answer' => ''
                                    , 'question' => ''
                                    , 'referrer' => $referrer
                                    , 'password1' => ''
                                    , 'password2' => ''
                                    , 6);
        
        $userid = $pw_recover_formvalues_array["userid"];
        
        if(!empty($userid))
           {
                $userid_counter = 0;
                $result_userid = db_exec("select * from user where userid= ?", array($userid));
                while ($row_userid = $result_userid->fetch())
                    {
                        $question = $row_userid["pw_recover_question"];
                        $record_id = $row_userid["id"];
                        $userid_counter++;
                    }
           }
        if(empty($userid))
            {
                $pw_recover_error_array["userid"] = "Error: no userid entered.";
                
                pw_recover1($pw_recover_formvalues_array,
                            $pw_recover_error_array);     
            }
            
        elseif(($userid_counter == 1) and
               (empty($question)))
            {
print_r("here");             
  $pw_recover_error_array["userid"] = "Error: the userid \"" .$userid. "\" was found, but no password recovery question was found. Please contact calendar@nsrc.org for further help.";
                
                pw_recover1($pw_recover_formvalues_array,
                            $pw_recover_error_array);    
            }
            
        elseif($userid_counter == 0)
            {
                $pw_recover_error_array["userid"] = "Error: the userid \"" .$userid. "\" was not found.";
                
                pw_recover1($pw_recover_formvalues_array,
                            $pw_recover_error_array);    
            }
            
        elseif($userid_counter > 1)
            {
                $pw_recover_error_array["userid"] = "Internal error! Please contact calendar@nsrc.org for help.";
                
                pw_recover1($pw_recover_formvalues_array,
                            $pw_recover_error_array);  
            }
            
        elseif(($userid_counter == 1) and
            (!empty($question)))
            {
                $pw_recover_formvalues_array["question"] = $question;
                $_SESSION["pw_recover_formvalues_array"] = $pw_recover_formvalues_array;
                 pw_recover2($pw_recover_formvalues_array,
                             $pw_recover_error_array);
            }
            
        else
            {
                $pw_recover_error_array["userid"] = "Internal error! Please contact calendar@nsrc.org for help.";
                
                pw_recover1($pw_recover_formvalues_array,
                            $pw_recover_error_array);  
            }
    }
    
elseif($SUBMIT == 'Submit Information')
    {   
        $pw_recover_error_array = array('userid' => ''
                                        , 'answer' => ''
                                        , 'password1' => ''
                                        , 'count' => ''
                                        , 4);
        
        $pw_recover_formvalues_array = array('userid' => $pw_recover_formvalues_array["userid"]
                                            , 'answer' => htmlspecialchars($_POST["answer"])
                                            , 'question' => $pw_recover_formvalues_array["question"]
                                            , 'referrer' => $pw_recover_formvalues_array["referrer"]
                                            , 'password1' => htmlspecialchars($_POST["password1"])
                                            , 'password2' => htmlspecialchars($_POST["password2"])
                                            , 6);

        $pw_recover_error_array = pw_recover_error_check($pw_recover_formvalues_array,
                                                        $pw_recover_error_array);
    
        if($pw_recover_error_array["count"] > 0)
            {
                 pw_recover2($pw_recover_formvalues_array,
                             $pw_recover_error_array);
            }
        else
            {
		$authenticated_user = $pw_recover_formvalues_array["userid"];
                $_SESSION["authenticated_user"] = $authenticated_user;
                
                $row_for_id = db_fetch1("select * from user where userid= ?", array($authenticated_user));
                $record_id = $row_for_id['id'];
                
                $new_md5_password = "MD5:" . strtoupper(md5($pw_recover_formvalues_array["password1"]));
                
                $result = db_update("update user set password = ? where id = ?", array($new_md5_password, $record_id));
        
                if(!$result[0])
                    {
                    echo "<p><center><strong>Unexpected error creating your updated user record. Please contact calendar@nsrc.org for help.</strong></center><p>. Return to Network Startup Resource Center Network Education Calendar <a href='/" . ROOT_DIR . "/index.php'>main page</a>.<p>\n";
                    echo "The error that occured was:<center>\n";
                    echo $result[1]. "</center><p><hr>\n";
                    }
                else
                    {
                        pw_recover_success($pw_recover_formvalues_array);
                    }
            }
    }
else
    {
        pw_recover1('',
                    '',
                    $referrer);
    }

?>
