<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Help: ICANN Region Specifcation (Network Startup Resource Center Network Education Calendar)</title>
</head>

<body bgcolor="#FFFFFF">


<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td>&nbsp;<table border="0" width="100%" align="center">
      <tr>
        <td width="100%" align="left"><font size="4" color="#000066">H</font><font size="2"
        color="#000066">ELP:</font> <font size="4" color="#000066">ICANN R</font><font size="2" color="#000066">egion</font> <font size="4" color="#000066">C</font><font size="2" color="#00000066">odes</font></td>
      </tr>
      <tr>
      <td align="left">
      <font size="2">Region codes based on the following <a href="http://www.icann.org/montreal/geo-regions-topic.htm" target="_blank">ICANN Specification</a>
      </td></tr>
    </table>

    <table border=1 align=center>
	 <tr>
   <td><b>Region</b></td>
   <td align=center><b>Country Name</b></td>
     </tr>

<?php
// For FILE_PATH
include "../config.php";

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

$result = db_exec("select * from country order by region", array());
while ($row = $result->fetch()) {
  echo "<tr><td align='left'>" . $row["region_name"]. "</td><td align='left'>" . $row["country_name"]. "</td></tr>\n";
}

?>

   </table>
<center><form><input type=button value=" Close Window " onClick="self.close();"></form></center>

</table>


<p>&nbsp;</p>
</body>
</html>
