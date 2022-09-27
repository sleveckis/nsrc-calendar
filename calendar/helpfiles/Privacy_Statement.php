<?php
ob_start();
session_start();
$session = session_id();

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

$header_title = "Network Startup Resource Center Network Education Calendar - Prvacy Statement";
$header_heading = "Privacy Statement";
$header_referrer = "/" . ROOT_DIR . "/helpfiles/Privacy_Statement.php";
include FILE_PATH . "/include/header.php";


if (isset($_SESSION['authenticated_user'])) {
  $authed_user = $GLOBALS["authenticated_user"];
  $row = db_fetch1("select * from user where id= ?", array($id));

}

?>

<tr><td class="content">

<hr width="700" align="left">

<br />

<table width="700" border="0" cellspacing="1" cellpadding="0">
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" width="698" bgcolor="#e3e3e3">

</td><td width="698" valign="middle" align="left">
<b class="title">Network Startup Resource Center Network Education Calendar Privacy Statement</b>
</td></tr>
<tr><td colspan="2">
<br>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
Information you enter in the etwork Startup Resource Center Network Education calendar will not be shared with any third parties outside of this web site. Other members of this site can see information that is given here for purposes of workshop collaboration.
<p>
Any registered user who distributes or sells any other member information for purposes other than workshop collaboration may have their account and use privileges removed.

<font size="-1">
<p>
<strong>Notes:</strong>

<ol>
<li>If you wish to change your userid please contact administrative staff via email at nsrc@nsrc.org. You will need to specify why the userid needs to be changed and verify your account information.</li>
<li>Other userid information, including your password, can be changed via the Network Startup Resource Center Network Education Calendar pages.</li>
</ol>

</tr></td>
</table>
</tr></tr>
</table>

<br>

<table border="0" width="698">
<tr><td align="center">
<form><input type=button value=" Close Window " onClick="self.close();"></form></form>
</td></tr>
</table>


</tr></td>
</table>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    //$our_filename = FILE_PATH . "/helpfiles/Privacy_Statement.php";
    include FILE_PATH . "/include/footer.php";
?>
