<?php

/* Verifies if the password fields are valid and equal  and
   updates the error array accordingly and returns true if 
   there are no errors */
function checkforpwerrors($formvalues_array, $error_array)
{

   // If either password field is empty tell the user.
   if ((empty($formvalues_array["password1"])) or (empty($formvalues_array["password2"])))
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
   elseif(strlen($formvalues_array["password1"]) < 6)
   {
   	 $error_array["count"] = $error_array["count"] + 1;
   	 $error_array["password"] = "Your password is too short! Your password should be at least 6 characters long." ;
   }
 
  return $error_array;
} // end checkforpwerrors

?>
