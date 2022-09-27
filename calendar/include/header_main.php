<?php

// header.php
//
// Hervey Allen for ISOC, July 2003
//
// Updated September, 2003
// Updated October 24, 2006 for new navigation bar
// Updated November 5, 2008 for new site look and feel
//

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Internet Society (ISOC) Workshop Resource Centre</title>
<link rel="shortcut icon" href="/calendar/images/favicon.ico"
type="image/x-icon" />
<meta name="description" content="The Internet Society (ISOC) is a
nonprofit organisation founded in 1992 to provide leadership in
Internet related standards, education, and policy. With offices in
Washington and Geneva, it is dedicated to ensuring the open
development, evolution and use of the Internet for the benefit of
people throughout the world" />
<meta name="keywords" content="Workshops, Network Workshops, Network
Training, Network Tutorials, Network Conferences, Network Training
calendar" />
<meta http-equiv="Content-Type" content="text/html;
charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="/css/main.css" />

</head>
<body>
<div class="top">
  <a href="http://www.isoc.org/" title="Internet Society Main
  Page"><img src="/cal-images/isoc_logo-189x68.jpg" alt="Internet Society
  Main Page" height="68" width="189" border="0"></a>
<a href="/" title="Internet Society Workshop
Resource Centre main page"><img src="/cal-images/wrc_logo_279x68.jpg"
  alt="ISOC Workshop Resource Centre" height="68" width="279" border="0"></a>


<div style="width: 1px; padding: 0px; height: 90px;"></div>
<div style="width: 110px">
	<a href="/search/" title="Search for materials">Search&#47;Site&nbsp;Map</a>
	<a HREF="/calendar/scripts/register.php" title="Create and manage accounts">Accounts</a>
</div>
<div style="width: 70px">
  	<a HREF="/materials/" title="A repository of information resources and educational materials for network training workshops">Materials</a>
	<a href="/planning/" title="Tools to aid in network workshop planning">Tools</a>
</div>
<div style="background-image:none">
  	<a HREF="/workshops/" style="text-align:right"
  title="Workshop sites hosted by the Workshop Resource Centre">Workshops</a>
	<a HREF="/calendar/" style="text-align:right" title="Upcoming and past network-related education and training events">calendar</a>
</div>
</div>

<?php


if($header_referrer == "/index.php")
    {
        $header_referrer = "?referrer=" .$header_referrer;
    }


if (session_is_registered("authenticated_user"))
    {
        $authed_user = $GLOBALS["authenticated_user"];
      
        $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
        $user_name = $row_name['name'];
?>

<table class="main">
  <tr><td colspan="2" align="right" valign="top">
    <font style="font-size: 10px;">

     <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/calendar/scripts/update.php<?php echo $header_referrer?>"><?php echo $user_name?></a> | <a href="https://<?php echo $_SERVER['SERVER_NAME']?>/calendar/scripts/update.php<?php echo $header_referrer?>">Update Profile</a> | <a href="/index.php?logout=TRUE">Logout</a></font>

    </font>
  <div class="line"></div>
</td></tr>
<tr>

<?php           
	} // end if
    
elseif (!session_is_registered("authenticated_user"))
       {
?>
<table class="main">
  <tr>
<td colspan="2" align="right" valign="top">
<font style="font-size: 11px;">
Site Maintained and Hosted by
</font>
<a href="http://nsrc.org/">Network Startup Resource Center</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <font style="font-size: 10px;">
<?php
       echo "<a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/register.php'>Create Account</a> | <a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/recover-password.php" .$header_referrer. "'>Recover Password</a> | <a href='https://" .$_SERVER['SERVER_NAME']. "/calendar/scripts/login.php" .$header_referrer. "'>Login</a></font>\n";
?>

    </font>
  <div class="line"></div>
</td></tr>
<tr>
<?php
	} //end elseif
    
// end of header script

?>
