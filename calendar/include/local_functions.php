<?php
//Let's set up some local functions to make life easier on us.

//Some Global Variables

//FILEPATH used to get root directory path
//$FILEPATH = realpath(dirname(__FILE__));
//$FILEPATH = substr($FILEPATH, 0, strpos($FILEPATH, '/include'));

//QUERY_DEBUG used by function safe_query
$QUERY_DEBUG = "Off";
// How many years out we allow for creation of new events from
// this year. I.E. offset =6 means this year +5 more.
$YEAROFFSET = 5;

//Function:	GET
//Source:	http://php.net/manual/en/reserved.variables.get.php
//Use:		GET function with input sanitized or false is returned if dangerous

// Smart GET function
function GET($name=NULL, $value=false, $option="default")
{
    $option=false; // Old version depricated part
    $content=(!empty($_GET[$name]) ? trim($_GET[$name]) : (!empty($value) && !is_array($value) ? trim($value) : false));
    if(is_numeric($content))
        return preg_replace("@([^0-9])@Ui", "", $content);
    else if(is_bool($content))
        return ($content?true:false);
    else if(is_float($content))
        return preg_replace("@([^0-9\,\.\+\-])@Ui", "", $content);
    else if(is_string($content))
    {
        if(filter_var ($content, FILTER_VALIDATE_URL))
            return $content;
        else if(filter_var ($content, FILTER_VALIDATE_EMAIL))
            return $content;
        else if(filter_var ($content, FILTER_VALIDATE_IP))
            return $content;
        else if(filter_var ($content, FILTER_VALIDATE_FLOAT))
            return $content;
        else
            return preg_replace("@([^a-zA-Z0-9\+\-\_\*\@\$\!\;\.\?\#\:\=\%\/\ ]+)@Ui", "", $content);
    }
    else false;
}

/*
DEFAULT: $_GET['page'];
SMART: GET('page'); // return value or false if is null or bad input
*/



//Function: 	safe_query
//Source:	MySQL/PHP Database Applications by Greenspan and Bulger
//Use:		You can set debug to "on" if you want more descriptive 
//		error codes while programming. 

function safe_query ($query = " ")
{
	if (empty($query)) { return FALSE; }

//	 if($QUERY_DEBUG = "Off")
//	 {
//		$local_result = mysql_query($query) or
//			die ("Query failed: please contact calendar@nsrc.org");
//	}
//	else
//	{
	$local_result = mysql_query($query)
		or die("Query failed: contact calendar@nsrc.org. Errors are as follows:"
			."<li>errorno=".mysql_errno()
			."<li>error=".mysql_error()
			."<li>query=".$query
		);
//	}
	return $local_result;
}


//Function:     pretty_mimetype
//Source:       Hervey Allen, NSRC, November 2008
//Use:          Take the full mimetype for files and convert it to
//              something a bit more human-readable.
//
//Notes:        The mimetypes available in the mimetype field in the
//              materials_item table are based on a php
//              function. Thus, as we update php more mimetypes
//              could appear in this field. So, occasionaly someone
//              should check the field in materials_item and add 
//              additional types here if new ones appear.


function pretty_mimetype ($mimet) {
  switch ($mimet)
    {
    case "text/html": $mimet = "HTML";
      break;
    case "application/vnd.ms-powerpoint": $mimet = "MS PowerPoint";
      break;
    case "application/pdf": $mimet = "PDF";
      break;
    case "pdf": $mimet = "PDF";
      break;
    case "text/plain": $mimet = "Text";
      break;
    case "application/postscript": $mimet = "Postscript";
      break;
    case "application/octet-stream": $mimet = "8-bit Data: binary or text";
      break;
    case "image/jpeg": $mimet = "JPEG image";
      break;
    case "application/mspowerpoint": $mimet = "MS PowerPoint";
      break;
    case "application/msword": $mimet = "MS Word";
      break;
    case "application/gzip": $mimet = "Gzip compressed file";
      break;
    case "application/zip": $mimet = "Zip compressed file";
      break;
    case "sxi": $mimet = "OpenOffice Presentation";
      break;
    case "application/x-tar": $mimet = "TAR archive";
      break;
    case "application/vnd.sun.xml.impress": $mimet = "OpenOffice Presentation";
      break;
    case "application/x-gzip": $mimet = "Gzip compressed file";
      break;
    case "application/x-diskcopy": $mimet = "disk image (iso, dmg, etc.)";
      break;
    case "application/vnd.stardivision.impress": $mimet = "OpenOffice Presentation";
      break;
    case "application/vnd.oasis.opendocument.presentation": $mimet = "OpenDocument Presentation";
      break;
    case "application/vnd.sun.xml.writer": $mimet = "OpenOffice Document";
      break;
    case "application/vnd.oasis.opendocument.text": $mimet = "OpenDocument Document";
      break;
    case "image/png": $mimet = "PNG image";
      break;
    case "application/vnd.ms-excel": $mimet = "MS Excel spreadsheet";
      break;
    case "application/x-bzip2": $mimet = "bzip2 Compressed archive";
      break;
    case "application/x-octet-stream": $mimet = "8-bit Data: binary or text";
      break;

    default:
      $mimet = "Unknown file type";
    }

  return $mimet;
} // end function pretty_mimetype




//Function:     textual_month
//Source:       Hervey Allen, NSRC, August 2005
//Use:          We'll use this in date_to_date and date_range to get 
//              a text version of our month.

function textual_month ($tmonth) {
     switch ($tmonth)
       {
       case "1": $tmonth = "Jan";
	 break;
       case "2": $tmonth = "Feb";
	 break;
       case "3": $tmonth = "Mar";
	 break;
       case "4": $tmonth = "Apr";
	 break;
       case "5": $tmonth = "May";
	 break;
       case "6": $tmonth = "Jun";
	 break;
       case "7": $tmonth = "Jul";
	 break;
       case "8": $tmonth = "Aug";
	 break;
       case "9": $tmonth = "Sep";
	 break;
       case "10": $tmonth = "Oct";
	 break;
       case "11": $tmonth = "Nov";
	 break;
       case "12": $tmonth = "Dec";
	 break;
       default:
	 $tmonth = "??";
       }
     return $tmonth;
}




//Function: 	date_to_date
//Source:	Hervey Allen, NSRC, September 2002
//Use:		This will format a date column in to the expected date
//		format for use with nsrc.org web page. The format is
//              "Updated: xn-MMM-YYYY" - or, 5-Aug-2001, and 10-Jan-1999,
//              Note that there is no leading "0".
//              Also Note, this is a different conversion from timestamp.

function date_to_date ($date)
{
  $formatted_year = substr($date,0,4);
  $formatted_month = substr($date,5,2);
  $formatted_day = substr($date,8,2);
     if (substr($formatted_day, 0, 1) == "0") {
       $formatted_day = substr($formatted_day, 1, 1);
     }

     $formatted_month=textual_month($formatted_month);

     $formatted_date = $formatted_day ."-". $formatted_month ."-". $formatted_year;

     return $formatted_date;
}



//Function: 	date_range
//Source:	Hervey Allen, NSRC, August 2005
//Use:		This will return a range of dates based on an ending
//              and beginning ts. Note, this means a decent string for
//              dates in the same month, different months, and different
//              years. This is not ISO formatted.

function date_range ($begin_date,
		     $end_date)
{
  $year_begin = substr($begin_date,0,4);
  $month_begin = substr($begin_date,5,2);
  $day_begin = substr($begin_date,8,2);

     if (substr($day_begin, 0, 1) == "0") {
       $day_begin = substr($day_begin, 1, 1);
     }

  $year_end = substr($end_date,0,4);
  $month_end = substr($end_date,5,2);
  $day_end = substr($end_date,8,2);

     if (substr($day_end, 0, 1) == "0") {
       $day_end = substr($day_end, 1, 1);
     }

  //
  // Convert our numeric month to 3-letter abbreviation format.
  //

  $month_begin = textual_month($month_begin);
  $month_end = textual_month($month_end);

     // Several possibilities of how to present our date range:
     //
     // Same month, same year, same day (uncommon)
     //

     if(($year_begin == $year_end) &&
	($month_begin == $month_end) &&
	($day_begin == $day_end))
       {
	 $date_range = $day_begin ."-". $month_begin ."-". $year_begin;
       }
     elseif(($year_begin == $year_end) &&
	    ($month_begin == $month_end))
       {
	 $date_range = $day_begin ." thru ". $day_end ."-".$month_begin ."-". $year_begin;
       }
     elseif($year_begin == $year_end)
       {
	 $date_range = $day_begin ."-". $month_begin ." thru ". $day_end ."-". $month_end ."-". $year_begin;
       }
     else
       {
	 $date_range = $day_begin ."-". $month_begin ."-". $year_begin ." thru ". $day_end ."-". $month_end ."-". $year_end;
       }

  return $date_range;
}


//Function: 	date_range_iso
//Source:	Hervey Allen, NSRC, June 2006
//Use:		This will return a range of dates based on an ending
//              and beginning ts. Note, this means a decent string for
//              dates in the same month, different months, and different
//              years. This is in ISO format.
//
//              Sample ISO format "2005 Oct 10-12"

function date_range_iso ($begin_date,
		     $end_date)
{
  $year_begin = substr($begin_date,0,4);
  $month_begin = substr($begin_date,5,2);
  $day_begin = substr($begin_date,8,2);

     if (substr($day_begin, 0, 1) == "0") {
       $day_begin = substr($day_begin, 1, 1);
     }

  $year_end = substr($end_date,0,4);
  $month_end = substr($end_date,5,2);
  $day_end = substr($end_date,8,2);

     if (substr($day_end, 0, 1) == "0") {
       $day_end = substr($day_end, 1, 1);
     }

  //
  // Convert our numeric month to 3-letter abbreviation format.
  //

  $month_begin = textual_month($month_begin);
  $month_end = textual_month($month_end);

     // Several possibilities of how to present our date range:
     //
     // Same month, same year, same day (uncommon)
     //

     if(($year_begin == $year_end) &&
	($month_begin == $month_end) &&
	($day_begin == $day_end))
       {
	 $date_range = $year_begin ." ". $month_begin ." ". $day_begin;
       }
     elseif(($year_begin == $year_end) &&
	    ($month_begin == $month_end))
       {
         $date_range = $year_begin ." ". $month_begin ." ". $day_begin ."-". $day_end;
       }
     elseif($year_begin == $year_end)
       {
	 $date_range = $year_begin ." ". $month_begin ." ". $day_begin ." - ". $month_end ." ". $day_end;
       }
     else
       {
	$date_range = $year_begin ." ". $month_begin ." ". $day_begin ." - ". $year_end ." ". $month_end ." ". $day_end;
       }

  return $date_range;
}



//Function: 	date_to_ts
//Source:	Hervey Allen, ISOC, October 2003
//Use:		This will format a date column to a UNIX timestamp. Note that
//              we don't have an actual time, so we set this to Noon, or '120000'
//

function date_to_ts ($date)
{
    $year = substr($date,0,4);
    $month = substr($date,5,2);
    $day = substr($date,8,2);
    
    $timestamp = $year.$month.$day. "120000";

     return $timestamp;
}


//Function: 	ts_to_date
//Source:	Hervey Allen, NSRC, September 2002
//Use:		This will format a ts column in to the expected datea
//		format for use with nsrc.org web page. The format is
//              "Updated: xn-MMM-YYYY" - or, 5-Aug-2001, and 10-Jan-1999,
//              Note that there is no leading "0".

function ts_to_date ($ts)
{
     $formatted_date = substr($ts,0,8);
     $formatted_year = substr($formatted_date, 0, 4);

     $formatted_month = substr($formatted_date, 4, 2);
     if (substr($formatted_month, 0, 1) == "0") {
       $formatted_month = substr($formatted_month, 1, 1);
     }

     $formatted_day = substr($formatted_date, 6, 2);
     if (substr($formatted_day, 0, 1) == "0") {
       $formatted_day = substr($formatted_day, 1, 1);
     }

     switch ($formatted_month)
       {
       case "1": $formatted_month = "Jan";
	 break;
       case "2": $formatted_month = "Feb";
	 break;
       case "3": $formatted_month = "Mar";
	 break;
       case "4": $formatted_month = "Apr";
	 break;
       case "5": $formatted_month = "May";
	 break;
       case "6": $formatted_month = "Jun";
	 break;
       case "7": $formatted_month = "Jul";
	 break;
       case "8": $formatted_month = "Aug";
	 break;
       case "9": $formatted_month = "Sep";
	 break;
       case "10": $formatted_month = "Oct";
	 break;
       case "11": $formatted_month = "Nov";
	 break;
       case "12": $formatted_month = "Dec";
	 break;
       default:
	 $formatted_month = "??";
       }

     $formatted_date = $formatted_day ."-". $formatted_month ."-". $formatted_year;

     return $formatted_date;
}


//Function: 	select_countries
//Source:	Hervey Allen, NSRC, September 2002
//Use:		Used in Report View to determine what countries the
//		report belongs to. We need to parse all country codes,
//              and then we need to create a "Countries" variable with
//              each countries name.

function select_countries ($countries)
{

  // First, let's be careful. If for some reason we have a malformed
  // string with a trailing ':', get rid of it. Note, you can use
  // the more powerful preg POSIX regular expression functions like
  // preg_split to do this, but then have fun reading the code...

  $pieces = trim($countries);
  $pieces = explode(":", $pieces);

  return $pieces;

}


//Function: 	select_countries
//Source:	Hervey Allen, NSRC, September 2002
//Use:		Used in Report View to determine what countries the
//		report belongs to. We need to parse all country codes,
//              and then we need to create a "Countries" variable with
//              each countries name.

function display_countries ($formvalues_array)
{

  // First, let's be careful. If for some reason we have a malformed
  // string with a trailing ':', get rid of it. Note, you can use
  // the more powerful preg POSIX regular expression functions like
  // preg_split to do this, but then have fun reading the code...

  $local_countries = $formvalues_array["countries"];

  echo "<b>countries = : </b>" .$local_countries. "<br>\n";

     $pieces = $countries;
     $pieces = trim($pieces);
     $pieces = explode(":", $pieces);
  echo "<b>pieces = : </b>" .$pieces. "<br>\n";
     $num_cc = count($pieces);

     echo "<b>num_cc = : </b>" .$num_cc. "<br>\n";

     for($i = 1; $i <= $num_cc; $i++)
       {

       $row2 = db_fetch1("select * from country where country_code= ?", array($pieces[$i]));

       echo "<b>result2 = :" .$result2. "</b><br>\n";
       echo "<b>row2[country_name] = :" .$row2["country_name"]. "</b><br>\n";

       $exploded_countries = $row2["country_name"];
       
       }

  return $exploded_countries;

}


//
// function privilege_level:
//

function privilege_level($appUsername)
{

if((isset($PHPSESSID)) && (isset($_SESSION["authenticatedUser"])))
  {
  $authedUser = $_SESSION["authenticatedUser"];
  $row = db_fetch1("select * from user where userid= ?", array($authedUser));

 if ($row && ($row["privilege"] == 1))
  {
    return 1;
  }
 elseif ($row && ($row["privilege"] == 0))
   {
     return 0;
   }
 elseif ($row && ($row["privilege"] != 0) && ($row["privilege"] != 1))
   {
     return -2;
   }
 else
   {
     // User is not logged in properly.
     return -1;

   } // end else
  } // end if

} // end priv_level



//
// function authenticate
//

function authenticate($authed_user, $authed_user_pw)
{

  if($authed_user == '')
    {
      $error_array["authed_user"] = "Username blank.";
    }

  if($authed_user_pw == '')
    {
      $error_array["authed_user_pw"] = "Password blank.";
    }

  if($authed_user != '')
    {
      $result = db_exec("select * from user where userid= ?", array($authed_user));
      if ($result->rowCount() === 1) 
	{
	  $error_array["authed_user"] = '';
	}
      else
	{
	  $error_array["authed_user"] = "Username \"" .$authed_user. "\" is invalid!";
	} 
    }

 if($error_array["authed_user"] == '')
   {

     $md5_password = "MD5:" . strtoupper(md5($authed_user_pw));

// If true, then the password/userid combination is correct.
     $result = db_exec("select *  from user where password = ? and userid = ?", array($md5_password, $authed_user));
     if ($result->rowCount() == 1)
       {
	 $error_array["authed_user_pw"] = '';
       }
     else
       {
	 $error_array["authed_user_pw"] = "Incorrect password.";
       }
   }
 else
   {
     $error_array["authed_user_pw"] = '';
   }

 return $error_array;

} // end function authenticate



//
// Function verify password. Just wanna know if the current user has
// given us a valid password.
//

function verify_password($authed_user, $user_pw)
    {
        
     if(empty($user_pw))
        {
            $result = 'EMPTY';
            
            return $result;
        }
        
     $md5_password = "MD5:" . strtoupper(md5($user_pw));

    $result = db_exec("select *  from user where password = ? and userid = ?", array($md5_password, $authed_user));
    if ($result->rowCount() == 1)
       {
	 $result = 'TRUE';
       }
     else
       {
	 $result = 'FALSE';
       }
   
        return $result;
    }



function country_lookup($ISO_code)
{
 $row_country = db_fetch1("select country_name from country where country_code= ?", array($ISO_code));
 return $row_country['country_name'];
}



function region_lookup($region_code)
{
 $row_region = db_fetch1("select long_name from regions where region= ?", array($region_code));
 return $row_region['long_name'];
}



function user_lookup($id)
{
  $row_user = db_fetch1("select name from user where id= ?", array($id));
  return $row_user['name'];
}


//
// Function language_lookup
//
// Find the actual language name based on the drop-down list position
//

function language_lookup($lang_id)
{
       $rowlang = db_fetch1("select * from languages where list_item_number= ?", array($lang_id));
       return $rowlang["language"];
}


//
// Function language_id_lookup
//
// Find the actual language id based on language name
//

function language_id_lookup($lang_name)
{
       $rowlang = db_fetch1("select * from languages where language= ?", array($lang_name));
       return $rowlang["list_item_number"];
}



//
// Function topic_id_lookup
//
// Find the actual topic id based on ltopic name
//

function topic_id_lookup($topic)
{
       $rowtopic = db_fetch1("select * from topics where topic= ?", array($topic));
       return $rowtopic["id"];
}


//
// Function workshop_lookup
//

function workshop_lookup($by_date, $by_region, $substring)
{

  //
  // We have three possible search methods with any combination of all three.
  // This function does the work of creating the MySQL query string first,
  // then doing the query, then returning the results in an array
  //

  if((!empty($by_date)) && (!empty($by_region)) && (!empty($substring)))
    {
      $query_string = "select * from workshop where year= ? AND (region= ? or region_secondary = ? or region_terciary= ?) AND locate(lcase(?),lcase(title)) ORDER by year DESC";
      $query_params = array($by_date, $by_region, $by_region, $by_region, $substring);
    }

  elseif((!empty($by_date)) && (!empty($by_region)) && (empty($substring)))
    {
      $query_string = "select * from workshop where year=? AND (region=? or region_secondary=? or region_terciary=?) ORDER by year DESC";
      $query_params = array($by_date,$by_region,$by_region,$by_region);
    }

  elseif((!empty($by_date)) && (empty($by_region)) && (empty($substring)))
    {
      $query_string = "select * from workshop where year= ? ORDER by year DESC";
      $query_params = array($by_date);
    }

  elseif((empty($by_date)) && (!empty($by_region)) && (!empty($substring)))
    {
      $query_string = "select * from workshop where (region=? or region_secondary=? or region_terciary=?) AND locate(lcase(?),lcase(title)) ORDER by year DESC";
      $query_params = array($by_region,$by_region,$by_region,$substring);
    }

  elseif((!empty($by_date)) && (empty($by_region)) && (!empty($substring)))
    {
      $query_string = "select * from workshop where year= ? AND locate(lcase(?),lcase(title)) ORDER by year DESC";
      $query_params = array($by_date,$substring);
    }

  elseif((empty($by_date)) && (empty($by_region)) && (!empty($substring)))
    {
      $query_string = "select * from workshop where locate(lcase(?),lcase(title)) ORDER by year DESC";
      $query_params = array($substring);
    }

  elseif((empty($by_date)) && (!empty($by_region)) && (empty($substring)))
    {
      $query_string = "select * from workshop where (region=? or region_secondary=? or region_terciary=?) ORDER by year DESC";
      $query_params = array($by_region,$by_region,$by_region);
    }

  elseif((empty($by_date)) && (empty($by_region)) && (empty($substring)))
    {
      echo "<strong><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>Query string is: </strong>";
      echo "No items selected!\n";
      echo "</font></font><br>\n";
      $query_string = "select * from workshop where 0 > 1";
      $query_params = array();
    }

  else
    {
      echo "<strong><font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>Query string is: </strong>";
      echo "Error: unknown condition encountered. Contact calendar@nsrc.org for help.\n";
      echo "</font></font><br>\n";
      $query_string = "select * from workshop where 0 > 1";
      $query_params = array();
    }

  return db_exec($query_string, $query_params);
}

//
// Prior error method using /scripts/errors.php is monolithic. This function display the error
// message passed, then determines if a login should be offered using the $LOGIN Bool variable. If
// So, then another message is display with a link to the correct login page based on the file where
// the error occurred.
//

function error_message($error_string,
                       $LOGIN,
                       $file_name)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - ERROR";
$header_heading = "ERROR";
$header_referrer = "local_functions.php";
include "$_SERVER[DOCUMENT_ROOT]/calendar/include/header.php";

?>


<tr><td>

<?php

 	echo "<br><font face='Verdana, Arial, Helvetica, sans-serif'><font size='4'><font color='#ff0000'><b>Error Detected:</b></font></font></font><p>\n";
       echo "<font face='Verdana, Arial, Helvetica, sans-serif'><font size='2'>\n";
       echo $error_string. "<p>\n";

       if($LOGIN == "login")
	  {
             echo "Use this login link <a href='/calendar/scripts/login.php?referrer=$file_name'>here</a>.\n";
	  }
       else  // offer up the main page
          {
	     echo "Click <a href='/index.php'>here</a> to return to the main page.\n";
          } 

       echo "</font></font>\n";
?>
</td></tr>
</table>

<br>

<?php
// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.


    $our_filename = $_SERVER[DOCUMENT_ROOT]."local_functions.php";
    include($_SERVER[DOCUMENT_ROOT].'footer.php');
?>

</body>
</html>

<?php 

} // end function error_with_login




?>
