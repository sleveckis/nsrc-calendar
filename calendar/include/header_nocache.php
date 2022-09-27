<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//
// Header file for pages that we don't want to cache.
//
// Hervey Allen for ISOC, Feb. 20004 
//

?>

<html>

<head>

<?php
  echo "<title>" .$header_title. "</title>\n";
?>

<meta NAME="AUTHOR" CONTENT="Internet Society">
<link REL="shortcut icon" HREF="images/favicon.ico" TYPE="image/x-icon">
</head>

<?php

//
// If we are showing main page header we need the larger logo, etc., else the rest of the
// site gets the other logo, etc.
//

if($header_referrer == "/calendar/index.php")
    {
?>
        <body background="images/bk-main.gif" bgcolor="#ffffff" link="#330099" vlink="#555555" alink="#dd7711">
        <table border="0" cellpadding="0" cellspacing="0" width="762">
        <tr><td width><img src="images/pixel.gif" height="1" width="12" border="0"></td>
        <td>
        <table border="0" cellpadding="0" cellspacing="0" width="742">
        <tr>
        <td align="left"><a href="http://www.isoc.org/"><img src="images/isoc.gif" width="208" height="83" border="0" alt="Internet Society"></a></td>
        <td width="379" align="center">       
<?php      
    }
else
    {
?>
        <body background="images/bk.gif" bgcolor="#ffffff" link="#330099" vlink="#555555" alink="#dd7711">
        <table border="0" cellpadding="0" cellspacing="0" width="762">
        <tr><td width><img src="images/pixel.gif" height="1" width="12" border="0"></td>
        <td>
        <table border="0" cellpadding="0" cellspacing="0" width="732">
        <tr>
        <td align="left"><a href="http://www.isoc.org/"><img src="images/isoc_small.gif" width="147" height="55" border="0" alt="Internet Society"></a></td>
        <td width="379" align="center">
<?php
    }
?>

<font face="Verdana, Arial, Helvetica, sans-serif"><font color="#000099"><font size="5">

<?php
  echo "<b>" .$header_heading. "</b></font></td>\n";
?>

</font></font></font>

</td></tr>
</table>

</td></tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="757">
<tr>
<td valign="middle" align="left" width="757">
<a href="/calendar/"><img src="images/new-nav/calendar-90x20.gif" border="0" width="90" height="20" alt="calendar"></a><a href="/workshops/"><img src="images/new-nav/workshops-93x20.gif" border="0" width="93" height="20" alt="workshops"></a><a href="/materials/"><img src="images/new-nav/educational_materials-156x20.gif" border="0" width="156" height="20" alt="Educational Materials"></a><a href="/planning/"><img src="images/new-nav/planning_tools-116x20.gif" border="0" width="116" height="20" alt="Planning Tools"></a><a href="/organizers/"><img src="images/new-nav/organizers-92x20.gif" border="0" width="92" height="20" alt="Organizers"></a><a href="/instructors/"><img src="images/new-nav/instructors-89x20.gif" border="0" width="89" height="20" alt="Instructors"></a><a href="/search/"><img src="images/new-nav/search-64x20.gif" border="0" width="64" height="20" alt="Search"></a><a href="/"><img src="images/new-nav/home-57x20.gif" border="0" width="57" height="20" alt="Home"></a>
</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="762">
<tr><td width><img src="images/pixel.gif" height="1" width="14" border="0"></td>

<?php

//
// This is important. The reset of the page is inside this cell, thus you must finish each page
// with '</td></tr>...'
//

echo "<td>\n";


if (session_is_registered("authenticated_user"))
    {
        $authed_user = $GLOBALS["authenticated_user"];
      
        $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
        $user_name = $row_name['name'];
?>
        <table border="0" cellpadding="0" cellspacing="0" width="742">
        <tr>
        <td align="left" valign="bottom" bgcolor="#ffffff" width="582">
        <font face='Verdana, Arial, Helvetica, sans-serif'>
        <font size="1">Welcome <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/calendar/scripts/update.php?referrer=<?php echo $header_referrer?>"><?php echo $user_name?></a></font></font>
        </td>
        <td align="right" valign="bottom" bgcolor="#ffffff" width="140">
        <font face='Verdana, Arial, Helvetica, sans-serif'>
        <font size="1"><a href="https://<?php echo $_SERVER['SERVER_NAME']?>/calendar/scripts/update.php?referrer=<?php echo $header_referrer?>">Update Profile</a> | <a href="<?php echo $header_referrer?>?logout=TRUE">Logout</a></font></font>
        </tr>
        </table>
<?php           
    }
    
elseif (!session_is_registered("authenticated_user"))
    {  
        echo "<table border='0' cellpadding='0' cellspacing='0' width='742'>\n";
        echo "<tr><td align='right' valign='bottom' bgcolor='#ffffff' width='742'>\n";
        echo "<font face='Verdana, Arial, Helvetica, sans-serif'>\n";
        echo "<font size='1'><a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/register.php'>Create Account</a> | <a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/recover-password.php?referrer=$header_referrer'>Recover Password</a> | <a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/login.php?referrer=$header_referrer'>Login</a></font></font>\n";
        echo "</td></tr>\n";
        echo "</table>\n";
    }
    
// end of header script
?>
