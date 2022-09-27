<?php
session_start();
$session = session_id();

// Functions used to verify data entered in the files upload page
// at /instructors/upload.php


function month_to_int($month_name)
{
    //
    // To use mktime we need our months to be integers. We go from
    // hardcoded names to ints this way...
    //

    switch ($month_name) {
        case 'January':
            $month_name_int = 1;
            break;
        case 'February':
            $month_name_int = 2;
            break;
        case 'March':
            $month_name_int = 3;
            break;
        case 'April':
            $month_name_int = 4;
            break;
        case 'May':
            $month_name_int = 5;
            break;
        case 'June':
            $month_name_int = 6;
            break;
        case 'July':
            $month_name_int = 7;
            break;
        case 'August':
            $month_name_int = 8;
            break;
        case 'September':
            $month_name_int = 9;
            break;
        case 'October':
            $month_name_int = 10;
            break;
        case 'November':
            $month_name_int = 11;
            break;
        case 'December':
            $month_name_int = 12;
            break;
    } // end switch

return $month_name_int;

} // end function month_to_into



function validcountrycode($cctld)
{

 $result = db_exec("select * from country where country_code= ?", array($cctld));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function validcountrycode.



function remove_checked($files_to_upload_array)
{
  $i= count($files_to_upload_array);
  $x = 0;

  if(($i == 0) &&
     (!empty($files_to_upload_array[$i]['filename'])))
     {
       unset ($files_to_upload_array[$i]);
     }

  for($a=0; $a <= $i; $a++)
    {
      if($files_to_upload_array[$a]["remove"] == 'on')
	{
	  unset ($files_to_upload_array[$a]);
        }
      else
        {
	  $temporary_files_to_upload_array[$x] = $files_to_upload_array[$a];
          $x++;
        }
    }

  return $temporary_files_to_upload_array;

} // end function remove_checked



//
// Look for errors in the form as filled out on the create workshop page.
//


function  checkforeventerrors($ws_create_error_array, $ws_create_formvalues_array)
{
  print_r("here");
 // If either userid field is empty tell the user.
  if (empty($ws_create_formvalues_array["title"]))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["title"] = "You need to enter a title for your workshop.";
    }

  if ((!empty($ws_create_formvalues_array["title"])) &&
      (!checkforalpha($ws_create_formvalues_array["title"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["title"] = "The title '" .$ws_create_formvalues_array["title"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter another title." ;
    }

//
// Check for either empty or invalid country codes.
//

  if((empty($ws_create_formvalues_array["country"])) &&
     (empty($ws_create_formvalues_array["location_tbd"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["country"] = "You have not entered a country code. Please do so. If you need help finding your country code use the link above.";
   }


 if((!empty($ws_create_formvalues_array["country"])) &&
    (!validcountrycode($ws_create_formvalues_array["country"])) &&
    (empty($ws_create_formvalues_array["location_tbd"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["country"] = "The country code '" .$ws_create_formvalues_array["country"]."' is not valid! Please use the link above to look up a valid country code.";
   }


//
// Verify we have something in the city field.
//

	if((empty($ws_create_formvalues_array["city"])) &&
	   (empty($ws_create_formvalues_array["location_tbd"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["city"] = "You have not entered a city name. Please do so.";
   }

  //
  // Verify we have valid text in the city field.
  //

  if ((!empty($ws_create_formvalues_array["city"])) &&
      (!checkforalpha($ws_create_formvalues_array["city"])) &&
      (empty($ws_create_formvalues_array["location_tbd"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["city"] = "The text entered '" .$ws_create_formvalues_array["city"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter city name again." ;
    }


//
// Deal with URL issues:
//

    if(empty($ws_create_formvalues_array["url"]))
        {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["url"] = "You have not entered a url for your workshop. Please do so.";
        }
        elseif ((!empty($ws_create_formvalues_array["url"])) &&
        (!checkforalpha($ws_create_formvalues_array["url"])))
        {
            $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
            $ws_create_error_array["url"] = "The URL '" .$ws_create_formvalues_array["url"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter another URL." ;
        }
 //   elseif((!empty($ws_create_formvalues_array["url"])) &&
   //        (checkforalpha($ws_create_formvalues_array["url"])))
   //     {
   //
            //
            // Code snippet taken from http://cl2.php.net/ereg_replace. Basically fixes bad
            // paths and adds 'http://' to web addresses. Code updated by Hervey Allen to
            // _not_ make the url a fully formed href.
            //

    //        $vdom = "[:alnum:]";                // Valid domain chars
    //        $vurl = $vdom."_~-";                // Valid subdomain and path chars
    //        $vura = $vurl."A-?a-y!#$%&*+,;=@."; // Valid additional parameters (after '?') chars;
    //                                // insert other local characters if needed
    //        $protocol = "[[:alpha:]]{3,10}://"; // Protocol exp
    //        $server = "([$vurl]+[.])+[$vdom]+"; // Server name exp
    //        $path = "(([$vurl]+([.][$vurl]+)*/)|([.]{1,2}/))*"; // Document path exp (/.../)
    //        $name = "[$vurl]+([.][$vurl]+)*";   // Document name exp
    //        $params = "[?][$vura]*";            // Additional parameters (for GET)
    //
    //        $input = $ws_create_formvalues_array["url"];
    //        $output1 = ereg_replace("($protocol)?($server(/$path($name)?)?)", "http://\\2", $input);
    //
    //        if($output1 != $ws_create_formvalues_array["url"])
    //            {
    //                $ws_create_error_array["url"] = "Informational only. URL changed to: '" .$output1. "'";
    //            }
    //    }

  //
  // Potential code snippet from http://www.zend.com/codex.php?id=201&single=1
  // that we could use to verify that entered url's are valid. Personally there
  // are too many possible odd url options to really want me to do much checking
  // on url's as we are more likely to just cause problems with valid url's.
  //
  //$phpnet = fsockopen("www.php.net", 80, &$errno, &$errstr, 30);
  //if(!$phpnet) {
  //change with your custom messages
  //echo "<b>php.net <font color=\"red\">down!!</font></b>\n"; }
  //else {
  //echo("<a href=\"http://www.php.net\">php.net</a>");
  //}



//
// User must pick at least a primary region for the workshop. Make sure this is not empty.
//

	if((empty($ws_create_formvalues_array["region1"])) &&
	   (empty($ws_create_formvalues_array["location_tbd"])))
	  {
	    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
	    $ws_create_error_array["region1"] = "You must indicate at least one primary region for this workshop. If only one region is chosen please use the 'Primary Region' drop-down list to indicate this region.";
	  }


//
// User has chosen more than one region and two of them, at least, are identical. Odd logic (there's got
// to be a better way) to deal with empty values.
//

   if((($ws_create_formvalues_array["region1"] == $ws_create_formvalues_array["region2"]) or
       ($ws_create_formvalues_array["region1"] == $ws_create_formvalues_array["region3"]) or
       ($ws_create_formvalues_array["region2"] == $ws_create_formvalues_array["region3"])) &
      ((!empty($ws_create_formvalues_array["region1"])) & ((!empty($ws_create_formvalues_array["region2"])) or (!empty($ws_create_formvalues_array["region3"])))) &
      (empty($ws_create_formvalues_array["location_tbd"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["region1"] = "You have chosen identical regions. Please choose distinct regions. If only one region is chosen please use the 'Primary Region' drop-down list to indicate the region.";
   }


   //
   // Large switch statement. There's too much extraneous date code to mix these
   // two cases together. If the date is uncertain, then date_tbd is set, otherwise
   // if the date is certain, we need to do a bunch of extra error checking.
   //

   if(!empty($ws_create_formvalues_array["date_tbd"]))
     {
       $date_certain = 0;
     }
   else
     {
       $date_certain = 1;
     }

   switch($date_certain)
     {
     case 0:
       // date is not certain

if(($ws_create_formvalues_array["begin_month"] == 'null') or
   ($ws_create_formvalues_array["begin_year"] == 'null'))
  {
        $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
        $ws_create_error_array["begin_date"] = "If final dates are to be determined, you must specify the month and year in the Start date drop-down lists.";
  }

       break;

     case 1:
       // date is certain

//
// Date checking goes in here. We have the following conditions we must check for:
//
// Without date_tbd checked
//


// User does not choose a date.
// User chooses an invalid date.
// User chooses an invalid date range (negative).
// User chooses a date range that's unreasonable (_long_)
//
// First step is to convert our date in to epoch (Unix) time.
// When we are all done in the actual db creation step we'll convert to MySQL date
// format.
//

//
// We need real year dates to start with. Yes, there's an easier way, but this works.
//

$hold_year = date('Y') +2;
$begin_year_real = $hold_year - $ws_create_formvalues_array["begin_year"];
$end_year_real = $hold_year - $ws_create_formvalues_array["end_year"];

//
// And some shorter variable names.
//

$begin_month = $ws_create_formvalues_array["begin_month"];
$begin_day = $ws_create_formvalues_array["begin_day"];
$end_month = $ws_create_formvalues_array["end_month"];
$end_day = $ws_create_formvalues_array["end_day"];


//
// And, to use mktime let's convert months to integers...
//

$begin_month_int = month_to_int($begin_month);
$end_month_int = month_to_int($end_month);

//
// OK, look for a null field (i.e. drop-down not chosen). This is a bunch of vars to
// check so, we'll set a var as well.
//

$date_not_chosen = 'False';

if(($ws_create_formvalues_array["begin_month"] == 'null') or
   ($ws_create_formvalues_array["begin_day"] == 'null') or
   ($ws_create_formvalues_array["begin_year"] == 'null') or
   ($ws_create_formvalues_array["end_month"] == 'null') or
   ($ws_create_formvalues_array["end_day"] == 'null') or
   ($ws_create_formvalues_array["end_year"] == 'null'))
    {
        $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
        $ws_create_error_array["begin_date"] = "You must choose both a start and an end date for this workshop. At least one date item from the drop-down lists is missing.";
        $date_not_chosen = 'True';
    }

//
// OK, at least two dates have been chosen. Let's create some epoch dates to manipulate.
//

if($date_not_chosen == 'False')
    {
        $epoch_time_start = mktime(0,0,0,$begin_month_int,$begin_day,$begin_year_real);
        $epoch_time_end = mktime(0,0,0,$end_month_int,$end_day,$end_year_real);

// convert back...
//        $begin = getdate($epoch_time_start);
//        $end = getdate($epoch_time_end);
//        printf('%s, %d, %d', $begin['month'], $begin['mday'], $begin['year']);
//        printf('%s, %d, %d', $end['month'], $end['mday'], $end['year']);
    }

//
// Let's see if the range between the two dates is valid. I.E., not a negative range.
//

if(($date_not_chosen == 'False') &&
   ($epoch_time_end < $epoch_time_start))
    {
        $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
        $ws_create_error_array["begin_date"] = "This is an invalid date range. Your 'Start Date' is after your 'End Date'.";
    }

//
// Let's see if the range between the two dates seems reasonable. We'll be extra cautious here and
// say that a workshop could go for 3 months. We may end up having to change this...
//

$diff_seconds = $epoch_time_end - $epoch_time_start;
$diff_weeks = floor($diff_seconds/604800);


if(($date_not_chosen == 'False') &&
    ($epoch_time_end > $epoch_time_start) &&
    ($diff_weeks > 12))
    {
        $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
        $ws_create_error_array["begin_date"] = "This does not appear to be a valid range of dates. Your workshop is " .$diff_weeks. " weeks long. If this is correct contact calendar@nsrc.org. We will adjust our maximum workshop length value.";
    }

//
// Now that we have reasonable date formats let's see if they are reasonable
// dates.
//

if(($date_not_chosen == 'False') &&
    (!checkdate($end_month_int, $end_day, $end_year_real)))
    {
      $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
      $ws_create_error_array["begin_date"] = "Your 'End Date' is incorrect. The day you chose does not exist for this month and year combination.";
    }

 if(($date_not_chosen == 'False') &&
    (!checkdate($begin_month_int, $begin_day, $begin_year_real)))
   {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["begin_date"] = "Your 'Start Date' is incorrect. The day you chose does not exist for this month and year combination.";
   }

     } // end switch

//
// User must pick at least a primary language. Make sure this is not empty.
//

  if(($ws_create_formvalues_array["language1"] == 'null') &&
      (empty($ws_create_formvalues_array["other_language"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["language1"] = "You must indicate at least one primary language for this workshop. If only one language is used please use the 'Primary Region' drop-down list to indicate this language.";
   }


//
// User has chosen more than one language and two of them, at least, are identical. Odd logic (there's got
// to be a better way) to deal with empty values.
//

   if((($ws_create_formvalues_array["language1"] == $ws_create_formvalues_array["language2"]) or
       ($ws_create_formvalues_array["language1"] == $ws_create_formvalues_array["language3"]) or
       ($ws_create_formvalues_array["language2"] == $ws_create_formvalues_array["language3"])) &
      (($ws_create_formvalues_array["language1"] != 'null') &
       (($ws_create_formvalues_array["language2"] != 'null') or
        ($ws_create_formvalues_array["language3"] !='null'))))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["language1"] = "You have chosen identical languages. Please choose distinct languages. If only one language is chosen please use the 'Primary Language' drop-down list to indicate the language.";
   }

//
// This basically works. What we are doing is if the user's language is not in the drop-down menus,
// then they can enter a language in the other_language field. If they enter junk, then either force
// them to type in something reasonable, or use the drop-down lists.
//
// There are several more error conditions you could write code to check, but this could be excessive:
//
// 1.) If user enters a language already in the drop-down lists. Maybe this is wrong... And, careful,
// you have to check for 2.) that the user has not already used all three drop-down lists, and/or you
// have to check that the language entered, even if available in the drop-down lists, is not already
// chosen - ugh - not worth it...
//
//
//

  if((!empty($ws_create_formvalues_array["other_language"])) &&
      (!checkforalpha($ws_create_formvalues_array["other_language"])))
   {
    $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
    $ws_create_error_array["other_language"] = "The language entry " .$ws_create_formvalues_array['other_language']. " does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter language name again, or indicate workshop language (if available) using drop-down lists.";
   }


  //
  // Verify we have valid text in the location field.
  //

  if ((!empty($ws_create_formvalues_array["location"])) &&
      (!checkforalpha($ws_create_formvalues_array["location"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["location"] = "The text entered '" .$ws_create_formvalues_array["location"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter location name again." ;
    }

//
// Deal with URL_secondary issues:
//


    if ((!empty($ws_create_formvalues_array["url_secondary"])) &&
        (!checkforalpha($ws_create_formvalues_array["url_secondary"])))
        {
            $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
            $ws_create_error_array["url_secondary"] = "The secondary URL '" .$ws_create_formvalues_array["url_secondary"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter another URL." ;
        }


  //
  // Verify we have valid text in the contact fields.
  //

  if ((!empty($ws_create_formvalues_array["contact_name"])) &&
      (!checkforalpha($ws_create_formvalues_array["contact_name"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["contact_name"] = "The text entered '" .$ws_create_formvalues_array["contact_name"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter contact name again." ;
    }

  if ((!empty($ws_create_formvalues_array["contact_email"])) &&
      (!checkforalpha($ws_create_formvalues_array["contact_email"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["contact_email"] = "The text entered '" .$ws_create_formvalues_array["contact_email"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter contact email again." ;
    }

 // Both methods to validate email essentially work, but both _fail_
 // if you enter in 'user@domain.' This fools them. A typical trick.
 // Code will be updated to check for this.

 if((!validate_email($ws_create_formvalues_array["contact_email"])) && (!empty($ws_create_formvalues_array["contact_email"])))
   //  if(!ereg("([[:alnum:]\.\-]+)(\@[[:alnum:]\.\-]+\.+)", $formvalues_array["email"]))
      {
       $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
       $ws_create_error_array["contact_email"] = "'" .$ws_create_formvalues_array["contact_email"]. "' does not appear to be a valid email address. Please try again.";
    }

  //
  // Verify we have valid text in the other/optional language field.
  //

  if ((!empty($ws_create_formvalues_array["other_language"])) &&
      (!checkforalpha($ws_create_formvalues_array["other_language"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["other_language"] = "The text entered '" .$ws_create_formvalues_array["other_language"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter your optional language name again." ;
    }


  //
  // Verify we have valid text in the comment field.
  //

  if ((!empty($ws_create_formvalues_array["text"])) &&
      (!checkforalpha($ws_create_formvalues_array["text"])))
    {
     $ws_create_error_array["count"] = $ws_create_error_array["count"] + 1;
     $ws_create_error_array["text"] = "The text entered '" .$ws_create_formvalues_array["text"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter your comments again." ;
    }

  return $ws_create_error_array;

} // end function checkforfileerrors



?>
