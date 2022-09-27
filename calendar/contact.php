<?php
ob_start();
session_start();
$session = session_id();

//$FILEPATH =  realpath(dirname(__FILE__));
include "config.php";

// Several housecleaning functions that we use throughout.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/local_functions.php";
include FILE_PATH . "/include/local_functions.php";

// Used to validate email entered in the form. Long function.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkemail.php";
//include "include/checkemail.php";
include FILE_PATH . "/include/checkemail.php";

// This is where we keep our form checking functions. These are long and
// involved, thus I did not want them in the main of this file, or as
// subroutines. Eventually most of the code in main should go here.
//include "$_SERVER[DOCUMENT_ROOT]/calendar/include/checkuser.php";
//include "include/checkuser.php";
include FILE_PATH . "/include/checkuser.php";

// Gets us connected to db. PW and userid are here. This is variable, so keep
// it in one file to avoid site-wide changes in the future.
include FILE_PATH . "/../calendar_include/connect.php";

function contact_page()
  {

// Include file for the document header. Locally you set the title of the page
// via the header_title variable, and you set the pages heading using the
// header_heading variable. Obviously if you don't set these, then they will
// be blank.

$header_title = "Network Startup Resource Center Network Education Calendar - Contact Us";
$header_heading = "Contact Us";
$header_referrer = "/" . ROOT_DIR . "/contact.php";
include FILE_PATH . "/include/header.php";

?>

<tr>

<td class="content">


<h3>Contact Us</h3>

<p class="tools">
   Please write to <font style="font-variant: small-caps; color: blue">calendar@nsrc.org</font> 
   with any questions, complaints, requests, suggestions, information, compliments or any other item 
   you wish to communicate. When writing, if you wish an answer, please consider giving information 
   such as:
<ul class="arrow" style="margin-bottom: 10px">
<li>Error messages you may have received. Copy and paste them in to your email.</li>
<li>Your organization and position.</li>
<li>Any other information to help us respond to you as quickly as possible.</li>
</ul>
This email address is monitored by multiple persons involved with maintaining and building this site. 
In general you should expect a response in a timely manner. As this is a global site differences in 
time zones may affect the speed of our response. Thank you for your interest!
</p>


</td>
</tr>
</table>

<?php
// Include file for the document footer. For the modified date of the 
// file we are in you need to pass this along to the footer program.

    //$our_filename = FILE_PATH . "/contact.php";
    include FILE_PATH . "/include/footer.php";
?>	

</body>
</html>

<?php	
 } // end function contact_page
?>




<?php
//
// Main
//

$logout = htmlspecialchars($_GET["logout"]);

if ($logout == TRUE)
    {
        session_destroy();    
        header("Location: /" . ROOT_DIR . "/contact.php");
    }
else
    {
        contact_page();
    }

?>
