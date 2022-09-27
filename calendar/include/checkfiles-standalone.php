<?php
session_start();
$session = session_id();

// Functions used to verify data entered in the files upload page
// at /instructors/upload.php


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
// Look for errors in the form as filled out on the file upload page.
//

function  checkforfileerrors($upload_file_error_array, $upload_file_formvalues_array)
{

 // If either userid field is empty tell the user.
  if (empty($upload_file_formvalues_array["title"]))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["title"] = "You need to enter in a descriptive title for your materials.";
    }

  if ((!empty($upload_file_formvalues_array["title"])) and
      (!checkforalpha($upload_file_formvalues_array["title"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["title"] = "The title '" .$upload_file_formvalues_array["title"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter another title." ;
    }

    if (empty($upload_file_formvalues_array["author"]))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["author"] = "You need to enter in author name(s) for these materials.";
    }

  if ((!empty($upload_file_formvalues_array["author"])) and
      (!checkforalpha($upload_file_formvalues_array["author"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["author"] = "The author name '" .$upload_file_formvalues_array["author"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter a valid author name." ;
    }

//
// Make sure user has chosen a month and a year:
//
// Note that "is_null" should work, but it doesn't seem reliable under php 4.x, so I do an explicit check instead.
//

    if(($upload_file_formvalues_array["month"] == 'null') and
        ($upload_file_formvalues_array["year"] != 'null'))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["year"] = "Please choose a month as well as a year for the date of these materials.";
    }

    if(($upload_file_formvalues_array["month"] != 'null') and
        ($upload_file_formvalues_array["year"] == 'null'))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["year"] = "Please choose a year as well as a month for the date of these materials.";
    }

    if(($upload_file_formvalues_array["month"] == 'null') and
        ($upload_file_formvalues_array["year"] == 'null'))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["year"] = "Please specify a date that represents when these materials were last current.";
    }

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
  // Otherwise, standard check to make sure there is some valid text in the
  // url field.
  //

  if ((!empty($upload_file_formvalues_array["url"])) &&
      (!checkforalpha($upload_file_formvalues_array["url"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["url"] = "The URL '" .$upload_file_formvalues_array["url"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter another URL." ;
    }


  //
  // Required. Check, first, that user has chosen a language, or filled one in.
  //

  if(($upload_file_formvalues_array["language1"] == 'null') and
      (empty($upload_file_formvalues_array["other_language"])))
    {
      $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
      $upload_file_error_array["language1"] = "No language choice made. Please choose a language, or enter one in the optional language box if your not language is not listed.";
    }

  //
  // Verify we have valid text in the other/optional language field.
  //

  if ((!empty($upload_file_formvalues_array["other_language"])) and
      (!checkforalpha($upload_file_formvalues_array["other_language"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["other_language"] = "The text entered '" .$upload_file_formvalues_array["other_language"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter your optional language name again.";
    }

  //
  // Verify that optional language filed and chosen language, if one is chosen are not
  // the same. Not really necessary, but easy enough to do...
  //
    if(((!empty($upload_file_formvalues_array["other_language"])) and
        ($upload_file_formvalues_array["language1"] != 'null')) and
        (strtolower(language_lookup($upload_file_formvalues_array["language1"])) == strtolower($upload_file_formvalues_array["other_language"])))
    {
        $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
        $upload_file_error_array["language1"] = "Your chosen language from the drop-down list and optional language are identical. Please remove your optional language entry.";
    }


//
// User has to pick or write in a topic, make sure they are not all empty.
//

  if(($upload_file_formvalues_array["topic1"] == 'null') and
      ($upload_file_formvalues_array["topic2"] == 'null') and
       ($upload_file_formvalues_array["topic3"] == 'null') and
        (empty($upload_file_formvalues_array["other_topic"])))
   {
    $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
    $upload_file_error_array["topic1"] = "You have not chosen or entered a topic. Please choose at least one topic, or write in a topic of your own.";
   }

//
// User must pick at least a primary topic. Make sure this is not empty.
//

  if(($upload_file_formvalues_array["topic1"] == 'null') and
      (empty($upload_file_formvalues_array["other_topic"])) and
      (($upload_file_formvalues_array["topic2"] != 'null') or
       ($upload_file_formvalues_array["topic3"] != 'null')))
   {
    $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
    $upload_file_error_array["topic1"] = "The Primary Topic menu is not chosen. Please choose your first topic from the drop-down lists from the Primary Topic menu first.";
   }


  //
  // Verify we have valid text in the other/optional topic field.
  //

  if ((!empty($upload_file_formvalues_array["other_topic"])) and
      (!checkforalpha($upload_file_formvalues_array["other_topic"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["other_topic"] = "The text entered '" .$upload_file_formvalues_array["other_topic"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter your optional topic description again." ;
    }


//
// User has chosen more than one topic and two of them, at least, are identical. Odd logic (there's got
// to be a better way) to deal with empty values.
//

   if((($upload_file_formvalues_array["topic1"] == $upload_file_formvalues_array["topic2"]) or
       ($upload_file_formvalues_array["topic1"] == $upload_file_formvalues_array["topic3"]) or
       ($upload_file_formvalues_array["topic2"] == $upload_file_formvalues_array["topic3"])) and
      (($upload_file_formvalues_array["topic1"] != 'null') and
       (($upload_file_formvalues_array["topic2"] != 'null') or
        ($upload_file_formvalues_array["topic3"] !='null'))))
   {
    $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
    $upload_file_error_array["topic1"] = "You have chosen identical topics. Please choose distinct topics.";
   }


  //
  // Finally, if the user has entered in valid text in the optional topic field, make sure it's not the same
  // as one of the drop down topic choices that are available. This is a bit more complex... Note, you should
  // really allow them to enter in identical text if they have already filled in all the other drop down fields.
  //

  if (((!empty($upload_file_formvalues_array["other_topic"])) and
      (checkforalpha($upload_file_formvalues_array["other_topic"]))) and
        (($upload_file_formvalues_array["topic1"] == 'null') or
         ($upload_file_formvalues_array["topic2"] == 'null') or
         ($upload_file_formvalues_array["topic3"] == 'null')))
    {
        $topic = strtolower($upload_file_formvalues_array["other_topic"]);
        $z = 0;
        $result_topic = db_exec("select * from topics where lcase(topic)= ?", array($topic));
        while ($row_topic = $result_topic->fetch())
            {
                $z++;
            }

            if($z > 0)
                {
                    $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
                    $upload_file_error_array["other_topic"] = "The topic you entered, \"" .$upload_file_formvalues_array["other_topic"]. "\" exists as a drop-down menu choice. Please choose this topic from the drop-down menus and remove it from the this Alternate Topic box to improve search results for your materials, thank you.";
                }
    }

  //
  // Verify we have valid text in the comment field.
  //

  if ((!empty($upload_file_formvalues_array["text"])) and
      (!checkforalpha($upload_file_formvalues_array["text"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["text"] = "The text entered '" .$upload_file_formvalues_array["text"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please enter your comments again." ;
    }


  //
  // Verify we have valid text in the file comment0 field.
  //

  if ((!empty($upload_file_formvalues_array["file_comment0"])) and
      (!checkforalpha($upload_file_formvalues_array["file_comment0"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["file_comment0"] = "Comment text '" .$upload_file_formvalues_array["file_comment0"]. "' had no valid characters (a-zA-Z0-9-_@!.) Please enter comment(s) again. You must choose the file again as well";
    }

  //
  // Verify we have valid text in the file comment1 field.
  //

  if ((!empty($upload_file_formvalues_array["file_comment1"])) and
      (!checkforalpha($upload_file_formvalues_array["file_comment1"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["file_comment1"] = "Comment text '" .$upload_file_formvalues_array["file_comment1"]. "' had no valid characters (a-zA-Z0-9-_@!.) Please enter comment(s) again. You must choose the file again as well";
    }

  //
  // Verify we have valid text in the file comment2 field.
  //

  if ((!empty($upload_file_formvalues_array["file_comment2"])) and
      (!checkforalpha($upload_file_formvalues_array["file_comment2"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["file_comment2"] = "Comment text '" .$upload_file_formvalues_array["file_comment2"]. "' had no valid characters (a-zA-Z0-9-_@!.) Please enter comment(s) again. You must choose the file again as well";
    }

  //
  // Verify we have valid text in the file comment3 field.
  //

  if ((!empty($upload_file_formvalues_array["file_comment3"])) and
      (!checkforalpha($upload_file_formvalues_array["file_comment3"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["file_comment3"] = "Comment text '" .$upload_file_formvalues_array["file_comment3"]. "' had no valid characters (a-zA-Z0-9-_@!.) Please enter comment(s) again. You must choose the file again as well";
    }

  //
  // Verify we have valid text in the file comment4 field.
  //

  if ((!empty($upload_file_formvalues_array["file_comment4"])) and
      (!checkforalpha($upload_file_formvalues_array["file_comment4"])))
    {
     $upload_file_error_array["count"] = $upload_file_error_array["count"] + 1;
     $upload_file_error_array["file_comment4"] = "Comment text '" .$upload_file_formvalues_array["file_comment4"]. "' had no valid characters (a-zA-Z0-9-_@!.) Please enter comment(s) again. You must choose the file again as well";
    }


  return $upload_file_error_array;

} // end function checkforfileerrors

?>
