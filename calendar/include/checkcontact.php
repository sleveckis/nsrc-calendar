<?php
session_start();
$session = session_id();

// Functions used to verify data entered in the the contact creation
// form, or /db/admin/signup.php

function loginexists($login_text)
{

 $result = db_exec("select * from contact where login= ?", array($login_text));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function loginexists.


function emailexists($email_text)
{

 $result = db_exec("select * from contact where email= ?", array($email_text));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function emailexists.


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

function validcountrycode($cctld)
{

 $result = db_exec("select * from country where country_code= ?", array($cctld));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function validcountrycode.


// Function checkforerrors
//
// We have a session and the user has entered data in the form. Time to
// check it's validity.

// Here's where we'll do all our error checking. This implies a SID

// Here is the array we'll use to keep track of error strings, and to quickly
// check if any errors are present.

function checkforerrors($error_array, $formvalues_array)
    {

 if (empty($formvalues_array["email"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["email"] = "You need to enter an email address below.";
    }

 if ((empty($formvalues_array["login"])) && (empty($formvalues_array["email"])))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["login"] = "No userid or email address entered. You must enter a valid userid, or valid email to act as your userid.";
    }

 if (!empty($formvalues_array["login"]))
   {
     if(!checkforalpha($formvalues_array["login"]))
       {
	 $error_array["count"] = $error_array["count"] + 1;
	 $error_array["login"] = "The chosen login '" .$formvalues_array["login"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please choose another login." ;
        }
     elseif (loginexists($formvalues_array["login"]))
       {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["login"] = "The chosen login '" .$formvalues_array["login"]. "' already exists! Please choose another login name. Or contact nsrc@nsrc.org for help." ;
       }
   }

 // Both methods to validate email essentially work, but both _fail_
 // if you enter in 'user@domain.' This fools them. A typical trick.
 // Code will be updated to check for this.

 if((!validate_email($formvalues_array["email"])) && (!empty($formvalues_array["email"])))
   //  if(!ereg("([[:alnum:]\.\-]+)(\@[[:alnum:]\.\-]+\.+)", $formvalues_array["email"]))
      {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' does not appear to be valid. Please try again.";
    }
      elseif(emailexists($formvalues_array["email"]))
   {
     if(empty($formvalues_array["login"]))
     {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' already exists, and since login was left empty this cannot be used as your login address. Contact nsrc@nsrc.org if you need help.";
     }
     elseif(!empty($formvalues_array["login"]))
     {
      $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' already has a login associated with it. Contact nsrc@nsrc.org if you do not remember your login.";
     }

   }

 if (empty($formvalues_array["name"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["name"] = "You need to enter a contact name below.";
    }

 if (!empty($formvalues_array["name"]))
   {
     if(!checkforalpha($formvalues_array["name"]))
     {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["name"] = "The chosen contact name '" .$formvalues_array["name"]. "' does not appear to contain valid alphanumeric characters. Please re-enter your contact name." ;
     }
   }

 // If either password field is empty tell the user.
 if ((empty($formvalues_array["password1"])) or (empty($formvalues_array["password2"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "You have left one of the password fields blank." ;
   }
 // If the passwords are not equal tell the user.
 elseif($formvalues_array["password1"] != $formvalues_array["password2"])
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "The passwords do not match! Please re-enter." ;
   }
 // If the password is less than 6 characters long tell the user.
 elseif(strlen($formvalues_array["password1"]) < 6)
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "Your password is too short! Your password should be at least 6 characters long." ;
   }
 // If the password does not contain any punctuation values, and it's longer than
 // 6 characters, then tell the user.
 elseif(!checkpassword($formvalues_array["password1"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "Your password does not contain any punctuation. Please add one of the following to your password: '_-!@.?$%:'." ;
   }

 // Let's make sure the user has entered something in the address fields. If not
 // warn them and ask for an address. Doing a full set of error checking on an
 // address is beyond our scope, but we can get something from the user.
 if((!empty($formvalues_array["address1"])) ||
    (!empty($formvalues_array["address2"])) ||
    (!empty($formvalues_array["address3"])) ||
    (!empty($formvalues_array["address4"])))
   {
     $address = TRUE;
   }

 // Then, if, none of the password fields contain any alphanumeric characters
 // (i.e. the user has entered pur gibberish) ask for something a bit more
 // reasonable.
 if(($address) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address1"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address2"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address3"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address3"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["address1"] = "None of your address fields appear to contain valid information. Please enter in a valid address or contact nsrc@nsrc.org for help." ;
   }

 if(empty($formvalues_array["country"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["country"] = "You have not entered in a country code! Please enter one in. Use the link above to look up a valid country code." ;
   }
 elseif(!validcountrycode($formvalues_array["country"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["country"] = "The country code '" .$formvalues_array["country"]."' is not valid! Please use the link above to look up a valid country code.";
   }

 //
 // ******* DEBUG ****** doubtful this is working! Fix it.
 //


 // If we have something in the phone field use 'ereg' to verify that there is at least
 // one integer in the string.
 if((!empty($formvalues_array["phone"])) && (!preg_match("/[0-9]*([0-9]+)[!0-9]*/", $formvalues_array["phone"])))
   //'[^0-9]*([0-9]+)[^0-9]*', $formvalues_array["phone"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["phone"] = "The phone number does not have any numbers in it.";
   }

 // If we have something in the fax field use 'ereg' to verify that there is at least
 // one integer in the string.
 if((!empty($formvalues_array["fax"])) && (!preg_match("/[0-9]*([0-9]+)[!0-9]*/", $formvalues_array["fax"])))
   //(!ereg('[^0-9]*([0-9]+)[^0-9]*', $formvalues_array["fax"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["fax"] = "The fax number does not have any numbers in it.";
   }

 return $error_array;

    } // end function checkforerrors

?>
