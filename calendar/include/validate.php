<?php
session_start();
$session = session_id();

function session_validity()
{
if(!isset($PHPSESSID)) 
{
  header("Location:login.php?nosession=TRUE");
    }

 if((isset($PHPSESSID)) && (!isset($authenticatedUser)))
    {
      header("Location:login.php?nouser=TRUE");
    }

 } // End function session_validity

?>
