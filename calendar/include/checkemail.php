<?php
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

?>
