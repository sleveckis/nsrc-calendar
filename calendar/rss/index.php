<?php
ob_start();
session_start();
$session = session_id();

include "../config.php";
// $FILEPATH =  realpath(dirname(__FILE__));
//
// Need to clean this up to not be hard-coded. We'll get burned at some point.
//


// Several housecleaning functions that we use throughout.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/local_functions.php";
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";


function rss_page()
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center (NSRC)- Network Education and Training calendar of Events RSS Feeds";
$header_heading = "Network Education and Training calendar of Events RSS Feeds";
$header_referrer = "/calendar/index.php";
//$FILEPATH =  realpath(dirname(__FILE__));
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/header.php";
include FILE_PATH . "/include/header.php";

	// Find total number of events for each category:

	// All
  	$result_all = db_exec("select * from workshop where deleted != 'Y' order by date_begin asc", array());
  	$num_rows_all = $result_all->rowCount();

	// Upcoming
  	$result_upcoming = db_exec("select * from workshop where (to_days(now()) - to_days(date_end) <= 0) order by date_begin asc", array());
  	$num_rows_upcoming = $result_upcoming->rowCount();

	// Previous
  	$result_previous = db_exec("select * from workshop where (to_days(now()) - to_days(date_end) >= 0) order by date_begin asc", array());
  	$num_rows_previous = $result_previous->rowCount();
?>


<table style="border:none;" cellpadding="0" cellspacing="0">
<tr style="border-top:none;"><td valign="top" align="left">
<p>We have made available two RSS feeds for you at this time. The feeds are updated hourly, so you
can simply reload them in your RSS compatible calendar program to see the latest updates. Either
just click on the links below, or right-click and copy the link location to your RSS compatible program.</p>
<td>&nbsp;&nbsp;</td>
</td><td style="text-align:right; vertical-align:top;" >
<!--
<img style="max-width:none; padding-right:10px; float: right;" src="/var/www/" . <?=ROOT_DIR?> . "/images/rss-logo-small.png"  alt="RSS logo" />
-->
<img style="max-width:none; padding-right:10px; float: right;" src="../images/rss-logo-small.png"  alt="RSS logo" />

 </td></tr>
</table>

<div style="text-align:left;">
<h3  style="font-size:1.4em;"  class="subHeadingBlue">Available RSS Feeds</h3>
<ul style="padding-left:30px;">
<li><a href="all-events.rss">ALL</a>: (<?php echo $num_rows_all?> events) A list of every network training
event past and future on this site.</li>
<li><a href="upcoming-events.rss">UPCOMING</a>: (<?php echo $num_rows_upcoming?> events) Upcoming network
training events from <?php echo date(DATE_RFC2822)?>.</li>
<!--
<li><a href="wrc-previous.ics">PREVIOUS</a>: (<?php echo $num_rows_previous?> events) Previous network
training events as of <?php echo date(DATE_RFC2822)?>.</li>
-->
</ul>

<h3 style="font-size:1.4em;" class="subHeadingBlue">A few more details</h3>
<p>
For each event we include:
</p>
<p>
<ul style="padding-left:30px;">
	<li>Title of the event</li>
	<li>URL</li>
	<li>Description</li>
	<li>Location (City, State if applicable, Country Code and Region)</li>
	<li>Dates of event</li>
	<li>Language</li>
	<li>Date of publication on our site</li>
</ul>
</p>
<p>
If the event location or final dates are still pending then this is noted as "TBD" (To Be Determined).
</p>
<p>
If you are interested in what programs support RSS calendar feeds, here are a few:
</p>
<p>
<ul style="padding-left:30px;">
<li><a href="https://www.google.com/search?q=rss+readers&oq=rss+readers">A list of RSS readers</a></li>
</ul>
</p>
<p>
These feeds are updated on an hourly basis.
</p>
<div>
<p class="boldItem"><a href="../index.php">Main calendar Page</a></p>
</div>

</div>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

//	$our_filename = $FILEPATH."/index.php";
	include FILE_PATH . "/include/footer.php"
?>

</body>
</html>

<?php
// End ical page, thus you need the </body> and </html>
// statements or you may get some interesting side affects
// dependent on your web server and subsequent page order.

    } // end function rss_page
?>

<?php
//
// Main
//

if(!isset($logout)){
$logout = FALSE;
}

if ($logout == TRUE)
    {
        session_destroy();
        header("Location: /" . ROOT_DIR . "/rss/index.php");
    }
else
    {
      rss_page();
    }
?>
