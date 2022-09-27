<?php

//$hostName = "localhost";
//$databaseName = "winestore2";
//$username = "hugh";
//$password = "drum";

function clean($input, $maxlength)
{
  $input = substr($input, 0, $maxlength);
  $input = EscapeShellCmd($input);
  return ($input);
}

?>
