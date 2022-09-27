<?php
session_start();
$session = session_id();

// Group all check functions together in one file, in progress.

// Functions used to verify data entered in the the contact creation
// form, or /db/admin/signup.php

function checkforalpha($text)
{
  // Right now we check that the string contains 'a-zA-Z0-9_-@!?'
  // This should be good enough for most cases, but ideally we
  // would like something like check that there is an alphanumeric
  // string, then we allow other characters.
  //
  // Updated to use preg_match instead. This is more compatible
  // across systems.
  //
  //  if(eregi("^[_a-z0-9-@!?]+(\.[_a-z0-9-@!?]+)*$", $text))
  // if((eregi("^[a-z0-9_-!@.]", $text)))

  if(preg_match("/[A-z0-9_\-\!\@\.]/", $text))
    {
      return TRUE;
    }
  else
    {
      return FALSE;
    }
} // end function checkforalpha.


function loginexists($login_text)
{

 $result = db_exec("select * from contact where login= ?", array($login_text));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function loginexists.


function emailexists($email_text)
{

 $result = db_exec("select * from contact where email= ?", array($email_text));
 if ($result->rowCount() == 1)
   {
     return TRUE;
   }
 else
   {
     return FALSE;
   }
} // end function emailexists.


function checkpassword($pw)
{
  // Need to switch to regular expression, but for now let's brute
  // force this! Much slower!
  //  if((eregi("^[_-!@.?$%:]$", $password_text)))
  //  if ((!strstr($pw, '_')) &
  //  (!strstr($pw, '-')) &
  //  (!strstr($pw, '!')) &
  //  (!strstr($pw, '@')) &
  //  (!strstr($pw, '.')) &
  //  (!strstr($pw, '?')) &
  //  (!strstr($pw, '$')) &
  //  (!strstr($pw, '%')) &
  //  (!strstr($pw, ';')) &
  //  (!strstr($pw, ':')))
  if(!preg_match("/[_\-\!\@\.\?\$\%\;\:]/", $pw))
    {
      return FALSE;
    }
  else
    {
      return TRUE;
    }
} // end function checkpassword.

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


// Function checkforerrors
//
// We have a session and the user has entered data in the form. Time to
// check it's validity.

// Here's where we'll do all our error checking. This implies a SID

// Here is the array we'll use to keep track of error strings, and to quickly
// check if any errors are present.

function checkforerrors($error_array, $formvalues_array)
    {

 if (empty($formvalues_array["email"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["email"] = "You need to enter an email address below.";
    }

 if ((empty($formvalues_array["login"])) && (empty($formvalues_array["email"])))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["login"] = "No userid or email address entered. You must enter a valid userid, or valid email to act as your userid.";
    }

 if (!empty($formvalues_array["login"]))
   {
     if(!checkforalpha($formvalues_array["login"]))
       {
	 $error_array["count"] = $error_array["count"] + 1;
	 $error_array["login"] = "The chosen login '" .$formvalues_array["login"]. "' does not appear to contain valid characters (a-zA-Z0-9-_@!.) Please choose another login." ;
        }
     elseif (loginexists($formvalues_array["login"]))
       {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["login"] = "The chosen login '" .$formvalues_array["login"]. "' already exists! Please choose another login name. Or contact nsrc@nsrc.org for help." ;
       }
   }

 // Both methods to validate email essentially work, but both _fail_
 // if you enter in 'user@domain.' This fools them. A typical trick.
 // Code will be updated to check for this.

 if((!validate_email($formvalues_array["email"])) && (!empty($formvalues_array["email"])))
   //  if(!ereg("([[:alnum:]\.\-]+)(\@[[:alnum:]\.\-]+\.+)", $formvalues_array["email"]))
      {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' does not appear to be valid. Please try again.";
    }
      elseif(emailexists($formvalues_array["email"]))
   {
     if(empty($formvalues_array["login"]))
     {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' already exists, and since login was left empty this cannot be used as your login address. Contact nsrc@nsrc.org if you need help.";
     }
     elseif(!empty($formvalues_array["login"]))
     {
      $error_array["count"] = $error_array["count"] + 1;
       $error_array["email"] = "The address'" . $formvalues_array["email"]. "' already has a login associated with it. Contact nsrc@nsrc.org if you do not remember your login.";
     }

   }

 if (empty($formvalues_array["name"]))
    {
      $error_array["count"] = $error_array["count"] + 1;
      $error_array["name"] = "You need to enter a contact name below.";
    }

 if (!empty($formvalues_array["name"]))
   {
     if(!checkforalpha($formvalues_array["name"]))
     {
       $error_array["count"] = $error_array["count"] + 1;
       $error_array["name"] = "The chosen contact name '" .$formvalues_array["name"]. "' does not appear to contain valid alphanumeric characters. Please re-enter your contact name." ;
     }
   }

 // If either password field is empty tell the user.
 if ((empty($formvalues_array["password1"])) or (empty($formvalues_array["password2"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "You have left one of the password fields blank." ;
   }
 // If the passwords are not equal tell the user.
 elseif($formvalues_array["password1"] != $formvalues_array["password2"])
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "The passwords do not match! Please re-enter." ;
   }
 // If the password is less than 6 characters long tell the user.
 elseif(strlen($formvalues_array["password1"]) < 6)
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "Your password is too short! Your password should be at least 6 characters long." ;
   }
 // If the password does not contain any punctuation values, and it's longer than
 // 6 characters, then tell the user.
 elseif(!checkpassword($formvalues_array["password1"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["password1"] = "Your password does not contain any punctuation. Please add one of the following to your password: '_-!@.?$%:'." ;
   }

 // Let's make sure the user has entered something in the address fields. If not
 // warn them and ask for an address. Doing a full set of error checking on an
 // address is beyond our scope, but we can get something from the user.
 if((!empty($formvalues_array["address1"])) ||
    (!empty($formvalues_array["address2"])) ||
    (!empty($formvalues_array["address3"])) ||
    (!empty($formvalues_array["address4"])))
   {
     $address = TRUE;
   }

 // Then, if, none of the password fields contain any alphanumeric characters
 // (i.e. the user has entered pur gibberish) ask for something a bit more
 // reasonable.
 if(($address) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address1"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address2"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address3"])) &&
    (!preg_match("/[A-z0-9]/", $formvalues_array["address3"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["address1"] = "None of your address fields appear to contain valid information. Please enter in a valid address or contact nsrc@nsrc.org for help." ;
   }

 if(empty($formvalues_array["country"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["country"] = "You have not entered in a country code! Please enter one in. Use the link above to look up a valid country code." ;
   }
 elseif(!validcountrycode($formvalues_array["country"]))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["country"] = "The country code '" .$formvalues_array["country"]."' is not valid! Please use the link above to look up a valid country code.";
   }

 //
 // ******* DEBUG ****** doubtful this is working! Fix it.
 //


 // If we have something in the phone field use 'ereg' to verify that there is at least
 // one integer in the string.
 if((!empty($formvalues_array["phone"])) && (!preg_match("/[0-9]*([0-9]+)[!0-9]*/", $formvalues_array["phone"])))
   //'[^0-9]*([0-9]+)[^0-9]*', $formvalues_array["phone"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["phone"] = "The phone number does not have any numbers in it.";
   }

 // If we have something in the fax field use 'ereg' to verify that there is at least
 // one integer in the string.
 if((!empty($formvalues_array["fax"])) && (!preg_match("/[0-9]*([0-9]+)[!0-9]*/", $formvalues_array["fax"])))
   //(!ereg('[^0-9]*([0-9]+)[^0-9]*', $formvalues_array["fax"])))
   {
    $error_array["count"] = $error_array["count"] + 1;
    $error_array["fax"] = "The fax number does not have any numbers in it.";
   }

 return $error_array;

    } // end function checkforerrors

  // end checkcontact.php

  // begin checkemail.php
  /*
  ********************************************************
  *** This script from MySQL/PHP Database Applications ***
  ***         by Jay Greenspan and Brad Bulger         ***
  ***                                                  ***
  ***   You are free to resuse the material in this    ***
  ***   script in any manner you see fit. There is     ***
  ***   no need to ask for permission or provide       ***
  ***   credit.                                        ***
  ********************************************************
  */

  #CheckEmail
  #
  #mailbox     =  addr-spec                    ; simple address
  #            /  phrase route-addr            ; name & addr-spec
  #
  #route-addr  =  "<" [route] addr-spec ">"
  #
  #route       =  1#("@" domain) ":"           ; path-relative
  #
  #addr-spec   =  local-part "@" domain        ; global address
  #
  #local-part  =  word *("." word)             ; uninterpreted
  #                                            ; case-preserved
  #
  #domain      =  sub-domain *("." sub-domain)
  #
  #sub-domain  =  domain-ref / domain-literal
  #
  #domain-ref  =  atom                         ; symbolic reference
  #
  #atom        =  1*<any CHAR except specials, SPACE and CTLs>
  #
  #specials    =  "(" / ")" / "<" / ">" / "@"  ; Must be in quoted-
  #            /  "," / ";" / ":" / "\" / <">  ;  string, to use
  #            /  "." / "[" / "]"              ;  within a word.
  #
  #                                            ; (  Octal, Decimal.)
  #CHAR        =  <any ASCII character>        ; (  0-177,  0.-127.)
  #ALPHA       =  <any ASCII alphabetic character>
  #                                            ; (101-132, 65.- 90.)
  #                                            ; (141-172, 97.-122.)
  #DIGIT       =  <any ASCII decimal digit>    ; ( 60- 71, 48.- 57.)
  #CTL         =  <any ASCII control           ; (  0- 37,  0.- 31.)
  #                character and DEL>          ; (    177,     127.)
  #CR          =  <ASCII CR, carriage return>  ; (     15,      13.)
  #LF          =  <ASCII LF, linefeed>         ; (     12,      10.)
  #SPACE       =  <ASCII SP, space>            ; (     40,      32.)
  #HTAB        =  <ASCII HT, horizontal-tab>   ; (     11,       9.)
  #<">         =  <ASCII quote mark>           ; (     42,      34.)
  #CRLF        =  CR LF
  #
  #LWSP-char   =  SPACE / HTAB                 ; semantics = SPACE
  #
  #linear-white-space =  1*([CRLF] LWSP-char)  ; semantics = SPACE
  #                                            ; CRLF => folding
  #
  #delimiters  =  specials / linear-white-space / comment
  #
  #text        =  <any CHAR, including bare    ; => atoms, specials,
  #                CR & bare LF, but NOT       ;  comments and
  #                including CRLF>             ;  quoted-strings are
  #                                            ;  NOT recognized.
  #
  #quoted-string = <"> *(qtext/quoted-pair) <">; Regular qtext or
  #                                            ;   quoted chars.
  #
  #qtext       =  <any CHAR excepting <">,     ; => may be folded
  #                "\" & CR, and including
  #                linear-white-space>
  #
  #domain-literal =  "[" *(dtext / quoted-pair) "]"
  #
  #
  #
  #
  #dtext       =  <any CHAR excluding "[",     ; => may be folded
  #                "]", "\" & CR, & including
  #                linear-white-space>
  #
  #comment     =  "(" *(ctext / quoted-pair / comment) ")"
  #
  #ctext       =  <any CHAR excluding "(",     ; => may be folded
  #                ")", "\" & CR, & including
  #                linear-white-space>
  #
  #quoted-pair =  "\" CHAR                     ; may quote any char
  #
  #phrase      =  1*word                       ; Sequence of words
  #
  #word        =  atom / quoted-string
  #

  #mailbox     =  addr-spec                    ; simple address
  #            /  phrase route-addr            ; name & addr-spec
  #route-addr  =  "<" [route] addr-spec ">"
  #route       =  1#("@" domain) ":"           ; path-relative
  #addr-spec   =  local-part "@" domain        ; global address

  #validate_email("insight\@bedrijfsnet.nl");

  // boolean validate_email ([string email address])

  // This function validates the format of an email address in a rather
  // exhaustive manner, based on the relevant RFC. Note: this does
  // NOT validate the email address in any functional way. Just because
  // it looks OK doesn't mean it works.

  function validate_email ($eaddr="")
  {

  	if (empty($eaddr))
  	{
  #print "[$eaddr] is not valid\n";
  		return false;
  	}
  	$laddr = "";
  	$laddr = $eaddr;

  # if the addr-spec is in a route-addr, strip away the phrase and <>s

  	$laddr = preg_replace('/^.*</','', $laddr);
  	$laddr = preg_replace('/>.*$/','',$laddr);
  	if (preg_match('/^\@.*:/',$laddr))	#path-relative domain
  	{
  		list($domain,$addr_spec) = preg_split('/:/',$laddr);
  		$domain = preg_replace('/^\@/','',$domain);
  		if (!is_domain($domain)) { return false; }
  		$laddr = $addr_spec;
  	}
  	return(is_addr_spec($laddr));
  }

  function is_addr_spec ( $eaddr = "" )
  {
  	list($local_part,$domain) = preg_split('/\@/',$eaddr);
  	if (!is_local_part($local_part) || !is_domain($domain))
  	{
  #print "[$eaddr] is not valid\n";
  		return false;
  	}
  	else
  	{
  #print "[$eaddr] is valid\n";
  		return true;
  	}
  }

  #local-part  =  word *("." word)             ; uninterpreted
  function is_local_part ( $local_part = "" )
  {
  	if (empty($local_part)) { return false; }

  	$bit_array = preg_split('/\./',$local_part);
  	while (list(,$bit) = each($bit_array))
  	{
  		if (!is_word($bit)) { return false; }
  	}
  	return true;
  }

  #word        =  atom / quoted-string
  #quoted-string = <"> *(qtext/quoted-pair) <">; Regular qtext or
  #                                            ;   quoted chars.
  #qtext       =  <any CHAR excepting <">,     ; => may be folded
  #                "\" & CR, and including
  #                linear-white-space>
  #quoted-pair =  "\" CHAR                     ; may quote any char
  function is_word ( $word = "")
  {

  	if (preg_match('/^".*"$/i',$word))
  	{
  		return(is_quoted_string($word));
  	}
  	return(is_atom($word));
  }

  function is_quoted_string ( $word = "")
  {
  	$word = preg_replace('/^"/','',$word);	# remove leading quote
  	$word = preg_replace('/"$/','',$word);	# remove trailing quote
  	$word = preg_replace('/\\+/','',$word);	# remove any quoted-pairs
  	if (preg_match('/\"\\\r/',$word))	# if ", \ or CR, it's bad qtext
  	{
  		return false;
  	}
  	return true;
  }


  #atom        =  1*<any CHAR except specials, SPACE and CTLs>
  #specials    =  "(" / ")" / "<" / ">" / "@"  ; Must be in quoted-
  #            /  "," / ";" / ":" / "\" / <">  ;  string, to use
  #            /  "." / "[" / "]"              ;  within a word.
  #SPACE       =  <ASCII SP, space>            ; (     40,      32.)
  #CTL         =  <any ASCII control           ; (  0- 37,  0.- 31.)
  #                character and DEL>          ; (    177,     127.)
  function is_atom ( $atom = "")
  {

  	if (
  	(preg_match('/[\(\)\<\>\@\,\;\:\\\"\.\[\]]/',$atom))	# specials
  		|| (preg_match('/\040/',$atom))			# SPACE
  		|| (preg_match('/[\x00-\x1F]/',$atom))		# CTLs
  	)
  	{
  		return false;
  	}
  	return true;
  }

  #domain      =  sub-domain *("." sub-domain)
  #sub-domain  =  domain-ref / domain-literal
  #domain-ref  =  atom                         ; symbolic reference
  function is_domain ( $domain = "")
  {

  	if (empty($domain)) { return false; }

  # this is not strictly required, but is 99% likely sign of a bad domain
  	if (!preg_match('/\./',$domain)) { return false; }

  	$dbit_array = preg_split('/./',$domain);
  	while (list(,$dbit) = each($dbit_array))
  	{
  		if (!is_sub_domain($dbit)) { return false; }
  	}
  	return true;
  }
  function is_sub_domain ( $subd = "")
  {
  	if (preg_match('/^\[.*\]$/',$subd))	#domain-literal
  	{
  		return(is_domain_literal($subd));
  	}
  	return(is_atom($subd));
  }
  #domain-literal =  "[" *(dtext / quoted-pair) "]"
  #dtext       =  <any CHAR excluding "[",     ; => may be folded
  #                "]", "\" & CR, & including
  #                linear-white-space>
  #quoted-pair =  "\" CHAR                     ; may quote any char
  function is_domain_literal ( $dom = "")
  {
  	$dom = preg_replace('/\\+/','',$dom);		# remove quoted pairs
  	if (preg_match('/[\[\]\\\r]/',$dom))	# bad dtext characters
  	{
  		return false;
  	}
  	return true;
  }

  // void print_validate_email ([string email address])

  // This function prints out the result of calling the validate_email()
  // function on a given email address.

  function print_validate_email ($eaddr="")
  {
  	$result = validate_email($eaddr) ? "is valid" : "is not valid";
  	print "<h4>email address (".htmlspecialchars($eaddr).") $result</h4>\n";
  }

// end checkemail.php

// begin checkevent.php

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

// end checkevent.php

// begin checkfiles-standalone.php

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

// end checkfiles-standalone.php

// begin checkfiles.php

// Functions used to verify data entered in the files upload page
// at /instructors/upload.php

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

// end checkfiles.php

// begin
