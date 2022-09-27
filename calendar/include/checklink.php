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
  if ((!strstr($pw, '_')) &&
      (!strstr($pw, '-')) &&
      (!strstr($pw, '!')) &&
      (!strstr($pw, '@')) &&
      (!strstr($pw, '.')) &&
      (!strstr($pw, '?')) &&
      (!strstr($pw, '$')) &&
      (!strstr($pw, '%')) &&
      (!strstr($pw, ';')) &&
      (!strstr($pw, ':')))
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

function checkforerrors($error_array, $formvalues_array, $status, $caller)
    {

      // It is critical to reset this here, otherwise even when you have
      // no errors you will still have the count set great than zero.
      $error_array["count"] = 0;

 if (empty($formvalues_array["title"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "You need to enter a title below.";
    }
 elseif((!eregi("^[a-z0-9_-!@.]", $formvalues_array["title"])))
   {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "Your title does not appear to have any acceptable alphanumeric characters in it.";
   }


 if (empty($formvalues_array["url"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["url"] = "No URL entered. Please enter in a URL.";
    }
 elseif((!eregi("^[a-z0-9_-!@.]", $formvalues_array["url"])))
   {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["url"] = "Your url does not appear to have any acceptable alphanumeric characters in it.";
   }

 if ($formvalues_array["category"] == 0)
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["category"] = "You must choose a category from the list below.";
    }

 if (!empty($formvalues_array["contact"]))
   {
      $contact_id = $formvalues_array["contact"];
      $result_error = db_exec("select * from contact where login= ?", array($contact_id));
      $num_rows = $result_error->rowCount();

      if($num_rows == 0)
	{
	  $error_array["count"] = $error_array["count"] + 1;
	  $error_array["contact"] = "The contact listed below does not exist in our database. Please contact nsrc@nsrc.org if you need help.";
	}
       elseif((!eregi("^[a-z0-9_-!@.]", $formvalues_array["contact"])))
	 {
	   $error_array["count"] = $error_array["count"] + 1;
	   $error_array["contact"] = "Your contact does not appear to have any acceptable alphanumeric characters in it.";
	 }
   }

 return $error_array;

    } // end function checkforerrors

?>
