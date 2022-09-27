<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help: ISO Code Directory (Network Startup Resource Center Network Education Calendar)</title>
</head>

<body bgcolor="#FFFFFF">


<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>&nbsp;<table border="0" width="100%" align="center">
      <tr>
        <td width="100%" align="left"><font size="4" color="#000066">H</font><font size="2"
        color="#000066">ELP:</font> <font size="4" color="#000066">ISO C</font><font size="2"
        color="#000066" >ODES</font></td>
      </tr>
    </table>

    <table border=1 align=center>
	 <tr>
   <td><b>Country Name</b></td>
   <td align=center><b>ISO Code</b></td>
     </tr>

<?php
// For FILE_PATH
include "../config.php";

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

$result = db_exec("select * from country order by country_name ASC", array());
while ($row = $result->fetch()) {
  echo "<tr><td align='left'>" . $row["country_name"]. "</td><td align='center'>" . $row["country_code"]. "</td></tr>\n";
}

?>

   </table>
<center><form><input type=button value=" Close Window " onClick="self.close();"></form></center>

</table>


<p>&nbsp;</p>
</body>
</html>
