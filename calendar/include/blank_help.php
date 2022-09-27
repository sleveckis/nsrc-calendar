<?php
ob_start();
session_start();
$session = session_id();

// Several housecleaning functions that we use throughout.
include('local_functions.php');

// Used to validate email entered in the form. Long function.
include('checkemail.php');

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as 
// subroutines. Eventually most of the code in main should go here.
include('checkuser.php');

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include('connect.php');


function choose_files($authed_user,
		      $user_name)
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Internet Society (ISOC) Workshop Resource Centre - Upload Workshop Files";
$header_heading = "Upload Workshop Files";
include('header_help.php');

?>


<table border="0" cellpadding="0" cellspacing="0" width="670">

<tr>
<td align="left" valign="bottom" bgcolor="#ffffff" width="500">
<font size='2'><a href="../scripts/update.php"><?php echo $user_name?></a> currently logged in:</font>
</tr>
</table>

<table width="670" border="0" cellspacing="1" cellpadding="0" bgcolor="#808080">
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" width="668" bgcolor="#e3e3e3">
<tr><td>
<font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><font color="#ff0000">
<b>File Upload Instructions</b></font></font></font>
<p>
<font face="Verdana, Arial, Helvetica, sans-serif"><font size="2">
Please browse for the file you wish to upload. Choose the file, and enter in the appropriate descriptive information. 
<P>
If you are uploading multiple files, then press the "Choose Another File" button, otherwise, if you have chosen all the files associated with your workshop materials, then please press "Upload Chosen File(s)".
</td></tr>
</table>
</td></tr>
</table>

<br>

testing...

<br>

<?php
// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

 $our_filename =  "/home/isoc/htdocs/instructors/file_chooser.php";

 include('footer.php');
?>

</body>
</html>

<?php 
// End file chooser page, thus you need the </body> and </html>
// statements or you may get some interesting side affects 
// dependent on your web server and subsequent page order.

    } // end function choose_files
?>

<?php
//
// Main
//


if(!session_is_registered("authenticated_user"))
{
  choose_files(null,
	       null);
}

elseif(session_is_registered("authenticated_user"))
{
  $authed_user = $GLOBALS["authenticated_user"];

  $row_name = db_fetch1("select * from user where userid= ?", array($authed_user));
  $user_name = $row_name['name'];

  choose_files($authed_user,
	       $user_name);
}
else
 {
   echo "<b>Not really sure why we are here...</b>\n";
 }

?>
