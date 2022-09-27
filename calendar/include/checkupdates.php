<?php


// Functions used to verify data entered in the the contact creation
// form, or /db/admin/signup.php


function loginexists($login_text)
{

 $result = db_exec("select * from user where userid= ?", array($login_text));
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

 $result = db_exec("select * from user where user= ?", array($email_text));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function emailexists.



// Function checkforupdateerrors
//
// We have a session and the user has entered data in the form. Time to
// check it's validity.

// Here's where we'll do all our error checking. This implies a SID

// Here is the array we'll use to keep track of error strings, and to quickly
// check if any errors are present.

function checkforupdateerrors($error_array, $formvalues_array)
    {


 // If either userid field is empty, but not both tell the user.
 if ((empty($formvalues_array["userid1"])) or (empty($formvalues_array["userid2"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["userid"] = "You have left one or both of the userid fields blank." ;
   }
 // If the userids are not equal tell the user.
 elseif($formvalues_array["userid1"] != $formvalues_array["userid2"])
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["userid"] = "The userids do not match! Please re-enter." ;
   }

 elseif ((!checkforalpha($formvalues_array["userid1"])) && (!checkforalpha($formvalues_array["userid2"])))
       {
	 $error_array["count"] = $error_array["count"] + 1;
	 $error_array["userid"] = "The chosen userid '" .$formvalues_array["userid1"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please choose another login." ;
       }


//
// If either password recovery field is empty tell the user.
//

 if ((empty($formvalues_array["pw_recover_question"])) or (empty($formvalues_array["pw_recover_answer"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["pw_recover"] = "You have left one or both of the password recovery fields blank." ;
   }

//
// Now check for valid (at least alphabetical) password question or answer fields.
//

 elseif ((!checkforalpha($formvalues_array["pw_recover_question"])) or (!checkforalpha($formvalues_array["pw_recover_answer"])))
    {
    	 $error_array["count"] = $error_array["count"] + 1;
	 $error_array["pw_recover"] = "Either the password recovery question, '" .$formvalues_array["pw_recover_question"]. "', or the password recover answer, '" .$formvalues_array["pw_recover_answer"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please change your text accordingly." ;
    }

 // Both methods to validate email essentially work, but both _fail_
 // if you enter in 'user@domain.' This fools them. A typical trick.
 // Code will be updated to check for this.

 if((!validate_email($formvalues_array["userid"])) && (!empty($formvalues_array["userid"])))
   //  if(!ereg("([[:alnum:]\.\-]+)(\@[[:alnum:]\.\-]+\.+)", $formvalues_array["email"]))
      {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["userid"] = "The userid'" . $formvalues_array["userid"]. "' does not appear to be a valid email address. Please try again.";
    }


 if (empty($formvalues_array["first_name"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["first_name"] = "You need to enter a first name below.";
    }

 elseif (!checkforalpha($formvalues_array["first_name"]))
    {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["first_name"] = "The chosen first name '" .$formvalues_array["first_name"]. "' does not appear to contain valid alphanumeric characters. Please re-enter your first name." ;
    }


 if (empty($formvalues_array["last_name"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["last_name"] = "You need to enter a last name below.";
    }

 elseif (!checkforalpha($formvalues_array["last_name"]))
     {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["last_name"] = "The chosen last name '" .$formvalues_array["last_name"]. "' does not appear to contain valid alphanumeric characters. Please re-enter your last name." ;
     }

//
// If both password fields are empty leave it alone and stop checking for errors, otherwise, check away!
//

   if((empty($formvalues_array["password1"])) && (empty($formvalues_array["password2"])))
    {
        // does php have a 'no op' ?
    }
    elseif(((!empty($formvalues_array["password1"])) && (empty($formvalues_array["password2"]))) or
       ((empty($formvalues_array["password1"])) && (!empty($formvalues_array["password2"]))))
     {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["password"] = "You have left one of the password fields blank." ;
     }
     // If the passwords are not equal tell the user.
     elseif($formvalues_array["password1"] != $formvalues_array["password2"])
     {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["password"] = "The passwords do not match! Please re-enter." ;
     }
     // If the password is less than 6 characters long tell the user.
     elseif((strlen($formvalues_array["password1"]) < 6))
     {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["password"] = "Your password is too short! Your password should be at least 6 characters long." ;
     }
     // If the password does not contain the first 4 characters of either the first or last names or the userid
     // then tell the user.
     elseif(!checkpassword($formvalues_array["password1"], $formvalues_array["first_name"], $formvalues_array["last_name"], $formvalues_array["userid"]))
     {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["password"] = "Your password matches the first 4 characters of either your userid, first name or last name";
     }



 return $error_array;

    } // end function checkforupdateerrors

  // checks if a user password matches the first 4 characters of either the first name, last name or userid
  // returns false if there is a match
function checkpassword($pw, $firstname, $lastname, $userid)
{
  // convert all the strings to uppercase to eliminate case issues
  $passwd = strtoupper($pw);
  $fname = strtoupper(substr($firstname,0,4));
  $lname = strtoupper(substr($lastname,0,4));
  $username = strtoupper(substr($userid,0,4));

  if ((preg_match($fname, $passwd)) || (preg_match($lname, $passwd)) || (preg_match($username, $passwd)))
    {
      return 0;
    }
  else
    {
      return 1;
    }
} // end function checkpassword.



?>
