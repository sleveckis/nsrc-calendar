<?php

// connect.php
//
// Hervey Allen for ISOC, Summer 2003
//

mysql_connect("localhost", "guest_nolog", "moL^tov") or
        die("Error " . mysql_errno() . " : " . mysql_error());
//	die ("Could not connect to database server for userid isoc.");
mysql_select_db ("isoc") or
	die ("Could not connect to database isoc.");
?>
