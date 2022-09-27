<?php
ob_start();
session_start();
$session = session_id();

// For FILE_PATH and ROOT_DIR
include "../config.php";

// Several housecleaning functions that we use throughout.
include FILE_PATH . "/include/local_functions.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";
//include "/var/www/sites/nsrc.org/calendar_include/connect.php";
//
// Used _after_ we have the materials_item table.
//
//

function ws_info($id,
                 $authed_user)
{
$header_title = "Network Startup Resource Center Network Education Calendar - Workshop Information";
$header_heading = "Workshop Information";
$header_referrer = "../helpfiles/workshop_info.php";
include FILE_PATH . "/include/header.php";

  $row = db_fetch1("select * from workshop where id= ?", array($id));

  echo "<tr><td class='content'>\n";

  echo "<h2>Summary Workshop Information</h2>\n";
  echo "<strong>For:</strong> <font color='#0000ff'><strong>" .$row['title']. "</strong></font>\n";

?>

<br />
<br />

Not all fields are required to be filled in. The following information is available for this workshop:

<p>&nbsp;

<table border="0" cellpadding="0" cellspacing="0" width="698">

<!-- TITLE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Title:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php echo $row['title']?>
</td></tr>

<!-- RECORD OWNER -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Record Owner:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<?php
	echo user_lookup($row['user_id']);
?>
</td></tr>

<!-- URL -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>URL:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php

	$parsed_url = parse_url($row['url']);
	if(empty($parsed_url['scheme']))
	{
		$event_url = "http://".$row['url'];
	}
	else
	{
		$event_url = $row['url'];
	}
	echo "<a href='" .$event_url. "'>" .$event_url. "</a>\n";?>
</td></tr>

<!-- SECONDARY URL -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Secondary URL:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php
    if($row['url_secondary'] == "")
      {
	echo "<font color='#006600'>None entered</font>\n";
      }
    else
      {

	$parsed_2url = parse_url($row['url_secondary']);

	if(empty($parsed_2url['scheme']))
	{
		$event_2url = "http://".$row['url_secondary'];
	}
	else
	{
		$event_2url = $row['url_secondary'];
	}


	echo "<a href='" .$event_2url. "'>" .$event_2url. "</a>\n";
      } // end else
?>
</td></tr>

<!-- COUNTRY -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>City, Country:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php

    if($row['location_tbd'] == 'on') {
        echo "TBD <br />";
    } else {
        if($row['remote']) {
            if(!empty($row['city']))
                echo $row['city'] . ", " . country_lookup($row['country']) . " (Virtual)";
            elseif(!empty($row['country']))
                echo country_lookup($row['country']) . " (Virtual)";
            else
                echo "(Virtual)";
            echo "<br />";
        } else {
            echo $row['city'] . ", " . country_lookup($row['country']);
            if($row['streaming'])
                echo " (Streaming)";
            echo "<br />";
        }
    }
?>
</td></tr>


<!-- REGION -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Region:</b>

<?php

//
// Lame, but appears to be required to force html to align
// td entities correctly...
//

if(!empty($row['region_secondary']))
    {
        echo "<br>&nbsp;\n";
    }

if(!empty($row['region_terciary']))
    {
        echo "<br>&nbsp;\n";
    }

?>


</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php

    if(empty($row['region']))
      {
	echo "<font color='#0000FF'>TBD</font>\n";
      }
 else
   {
     echo $row['region'];
   }

 if((!empty($row['region_secondary'])) &&
    (!empty($row['region'])))
   {
     echo "<br>" .$row['region_secondary']. "\n";
   }

 if((!empty($row['region_terciary'])) &&
    (!empty($row['region'])))
   {
     echo "<br>" .$row['region_terciary']. "\n";
   }

?>
</td></tr>

<!-- START DATE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Start Date:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php

    if($row['date_tbd'] == 'on')
      {
	$date_to_show = substr($row['month'],0,3)."-".$row['year']." (<font color='#0000FF'>TBD</font>)\n";
	echo $date_to_show;
      }
    else
      {
	echo $row['date_begin'];
      }
?>
</td></tr>

<!-- END DATE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>End Date:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php

    if($row['date_tbd'] == 'on')
      {
	echo "<font color='#0000FF'>TBD</font>\n";
      }
    else
      {
	echo $row['date_end'];
      }

?>
</td></tr>

<!-- PRIMARY LANGUAGE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Primary Language:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php echo $row['language1']?>
</td></tr>

<!-- SECONDARY LANGUAGE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Secondary Language:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php
    if($row['language2'] == "")
      {
	echo "<font color='#006600'>Not selected</font>\n";
      }
    else
      {
	echo $row['language2'];
       }
?>
</td></tr>

<!-- TERTIARY LANGUAGE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Tertiary Language:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php
    if($row['language3'] == "")
      {
	echo "<font color='#006600'>Not selected</font>\n";
      }
    else
      {
	echo $row['language3'];
       }
?>
</td></tr>

<!-- OTHER LANGUAGE -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Other Language:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php
    if($row['other_language'] == "")
      {
	echo "<font color='#006600'>None entered</font>\n";
      }
    else
      {
	echo $row['other_language'];
       }
?>
</td></tr>

<!-- LOCATION -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Location:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>
<?php
    if($row['location'] == "")
      {
	echo "<font color='#006600'>None entered</font>\n";
      }
    else
      {
	echo $row['location'];
      }
?>
</td></tr>

<!-- CONTACT NAME -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Contact Name:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<?php

            echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
	    if($row['contact_name'] == "")
	      {
		echo "<font color='#006600'>None entered</font>\n";
	      }
	    else
	      {
		echo $row['contact_name'];
	      }
            echo "</font></font>\n";
?>
</td></tr>

<!-- CONTACT EMAIL -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Contact Email:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<?php
    if($row['contact_email'] == "")
      {
	echo "<font color='#006600'>None entered</font>\n";
      }
    else
      {
	echo $row['contact_email'];
	//echo "\n";
      }
 echo "</font></font>\n";
?>
</td></tr>

<!-- COMMENTS -->

<tr><td width = 20% valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'><font color="#0000ff">
<b>Comments:</b>
</font></font></font>
</td>

<td width='5%' valign='top'>
   &nbsp;
</td>

<td valign='top'>
<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>

<?php
    if(!empty($row['comment']))
      {
	$comment_text = html_entity_decode(stripslashes($row['comment']));
	echo "<textarea rows='8' name='text' cols='64' readonly>\n";
	echo $comment_text;
	echo "</textarea></font>\n";
      }
 else
   {
     echo "<font color='#006600'>No comments</font>\n";
       }
?>

</td></tr>

</td></tr>
</table>
</td></tr>
</table>

<p>

<table width="700" border="0">
<tr><td>
<center><form><input type=button value=" Close Window " onClick="self.close();"></form></center>
</td><tr>
</table>

<?php
// Include file for the document footer. For the modified date of the
// file we are in you need to pass this along to the footer program.

    //$our_filename = $_SERVER[DOCUMENT_ROOT]."/calendar/helpfiles/workshop_info_main.php";
    include FILE_PATH . "/include/footer.php";

echo "</body>\n";
echo "</html>\n";

} // end function ws_info

?>



<?php
//
// Main
//

$id = htmlspecialchars($_GET["id"]);
if (isset( $_SESSION['authenticated_user'] ))
      {
	$authed_user = $_SESSION["authenticated_user"];

	$row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
	$user_name = $row_name['name'];

	ws_info($id,
                $authed_user);
      }
elseif(!isset( $_SESSION['authenticated_user'] ))
    {
        ws_info($id,
                '');

    }

?>
