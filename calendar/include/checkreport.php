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

function checkforerrors($error_array, $formvalues_array, $status, $caller)
    {

      // It is critical to reset this here, otherwise even when you have
      // no errors you will still have the count set greater than zero.
      //
      // And, technically not required, we clear all the error fields here
      // as attemtping to debug code with left over errors is hard.

       // start debug
      echo "In checkreport. Error array settings are: \n\n";
        echo "<b>error count > 0, submit = next, is set reportID</b><br>\n";
	echo "\n\n Errors are: \n";
	echo "\n<b>count: " .$error_array["count"]. "\n";
	echo "\n<b>title: " .$error_array["title"]. "\n";
	echo "\n<b>countries: " .$error_array["countries"]. "\n";
	echo "\n<b>author: " .$error_array["author"]. "\n";
	echo "\n<b>contact: " .$error_array["contact"]. "\n";
	echo "\n<b>category: " .$error_array["category"]. "\n";
	echo "\n<b>timestamp: " .$error_array["timestamp"]. "\n\n";
	echo "We are now about to reset the error array in checkreport.php\n\n";
	// end debug

      $error_array["count"] = 0;
      $error_array["title"] = '';
      $error_array["countries"] = '';
      $error_array["author"] = '';
      $error_array["contact"] = '';
      $error_array["category"] = '';
      $error_array["timestamp"] = '';


       // start debug
      echo "In checkreport. Error array settings after resetting are: \n\n";
        echo "<b>error count > 0, submit = next, is set reportID</b><br>\n";
	echo "\n\n Errors are: \n";
	echo "\n<b>count: " .$error_array["count"]. "\n";
	echo "\n<b>title: " .$error_array["title"]. "\n";
	echo "\n<b>countries: " .$error_array["countries"]. "\n";
	echo "\n<b>author: " .$error_array["author"]. "\n";
	echo "\n<b>contact: " .$error_array["contact"]. "\n";
	echo "\n<b>category: " .$error_array["category"]. "\n";
	echo "\n<b>timestamp: " .$error_array["timestamp"]. "\n\n";
	echo "End debug *********************** \n\n\n";
	// end debug


      //
      // I've rearranged the code here. 'admin_user' only checks come first:
      //

      //
      // Contact check
      //

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
	    elseif(!preg_match("/[A-z0-9_\-\!\@\.]/", $formvalues_array["contact"]))
    //	    elseif((!eregi("^[a-z0-9_-!@.]", $formvalues_array["contact"])))
	      {
		$error_array["count"] = $error_array["count"] + 1;
		$error_array["contact"] = "Your contact does not appear to have any acceptable alphanumeric characters in it.";
	      }
	  }

 //
 // Category check
 //

	if (($formvalues_array["category"] == 0) && ($caller = "admin_user"))
	  {
	    $error_array["count"] = $error_array["count"] + 1;
	    $error_array["category"] = "You must choose a category from the list below.";
	  }


 //
 // Below are items that both admin and normal users must have checked.
 //

      //
      // Title check.
      //

 if (empty($formvalues_array["title"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "You need to enter a title below.";
    }
 elseif(!preg_match("/[A-z0-9_\-\!\@\.]/", $formvalues_array["title"]))
   // elseif((!eregi("^[a-z0-9_-!@.]", $formvalues_array["title"])))
   {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "Your title does not appear to have any acceptable alphanumeric characters in it.";
   }

 //
 // Country check
 //

   if (empty($formvalues_array["countries"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["countries"] = "You need to add at least one country for this report.";
    }

 //
 // Author check.
 //

   if ((!empty($formvalues_array["author"])) && (!preg_match("/[A-z0-9_\-\!\@\.]/", $formvalues_array["author"])))
     //((!eregi("^[a-z0-9_-!@.]", $formvalues_array["author"]))))
	 {
	   $error_array["count"] = $error_array["count"] + 1;
	   $error_array["author"] = "Your listed author does not appear to have any acceptable alphanumeric characters.";
	 }

 return $error_array;

    } // end function checkforerrors




//


// Function checkforerrors_normal_user
//
// We have a session and the user has entered data in the form. Time to
// check it's validity.

// Here's where we'll do all our error checking. This implies a SID

// Here is the array we'll use to keep track of error strings, and to quickly
// check if any errors are present.

function checkforerrors_normal_user($formvalues_array, $error_array)
    {

      // It is critical to reset this here, otherwise even when you have
      // no errors you will still have the count set greater than zero.
      //
      // And, technically not required, we clear all the error fields here
      // as attemtping to debug code with left over errors is hard.

      $error_array["count"] = 0;
      $error_array["title"] = '';
      $error_array["countries"] = '';
      $error_array["author"] = '';
      $error_array["text"] = '';


      //
      // Title check.
      //

      // You can't guarrantee that ereg is going to be available, so
      // these functions swithced to use pcre with preg_match. Note,
      // these are enabled by default in php 4.2, and higher.
      //
      // Function is built off example found at:
      // http://www.zend.com/zend/tut/tutorial-delin2.php?print=1
      // (Feb. 23, 2003)
      //

 if (empty($formvalues_array["title"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "You need to enter a title below.";
    }
 elseif(!preg_match("/[A-z0-9_\-\!\@\.]/", $formvalues_array["title"]))
   {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["title"] = "Your title does not appear to have any acceptable alphanumeric characters in it.";
   }

 //
 // Country check.
 //

   if (empty($formvalues_array["countries"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
     $error_array["countries"] = "You need to add at least one country for this report.";
    }

 //
 // Author check.
 //

   if ((!empty($formvalues_array["author"])) && (!preg_match("/[A-z0-9_\-\!\@\.]/", $formvalues_array["author"])))
	 {
	   $error_array["count"] = $error_array["count"] + 1;
	   $error_array["author"] = "Your listed author does not appear to have any acceptable alphanumeric characters.";
	 }

 return $error_array;

    } // end function checkforerrors_normal_user


?>
