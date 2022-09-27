<?php
// form.php

if($_SESSION['error']['count'] == 1) {
?>
    <p>
        <b style='color: red'>
            1 error was detected!
            <br>
            Please check your form below and correct the indicated errors as necessary.
        </b>
    </p>
<?php
} elseif($_SESSION['error']['count'] > 1) {
?>
    <p>
        <b style='color: red'>
            <?=$_SESSION['error']['count']?> errors were detected!
            <br>
            Please check your form below and correct the indicated errors as necessary.
        </b>
    </p>
<?php
}
?>

<form method='POST' action='' />
    <table class='form_table'>
        <tr>
            <td>
                <b style='font-size: 12pt; color: red'>General Information</b>
                <p>
                Core event items will be presented first. Generally the more information you can give us, the easier it
                will be for others to find your event entry in the future. You can <a href='./update.php'>update your
                event entry</a> at a later date if you need to. You must be logged in on the account that originally
                created the entry.
                </p>
                
                <p>
                Required items will be marked with an asterisk (*). If you select 'Virtual' for Attendance Type,
                location information is <i>optional</i>.
                </p>
                <table style='min-width: 750px; width: 750px; margin: auto'>
                    
                    <!-- Event Name -->
                    <tr>
                        <td width='20%'>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_title.php' target='_blank'>Event Name*</a>:
                        </td>

                        <td>
                                Please enter the official name of your event here.
<?php
if(!empty($_SESSION['error']['title'])) {
    errormessage($_SESSION['error']['title']);
}
?>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['title'])) {
    echo "<input type='text' name='title' size='32' value='" . $_SESSION['form']['title'] . "' maxlength='255' />";
} else {
    echo "<input type='text' name='title' size='32' value='' maxlength='255' />";
}
?>
                        </td>
                    </tr>

                    <!-- Event URL -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_url.php' target='_blank'>Event URL*</a>:
                        </td>

                        <td>
                            Event website, or a URL where users can get more information about the event. You can
                            enter an additional URL in the <i><b>Additional Information</b></i> section below. Be
                            sure to include the 'http://' or 'https://'.
<?php
if(!empty($_SESSION['error']['url'])) {
    errormessage($_SESSION['error']['url']);
}
?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['url'])) {
    echo "<input type='text' name='url' size='32' value='" . $_SESSION['form']['url'] . "' maxlength='96' placeholder='https://example.com'/>";
} else {
    echo "<input type='text' name='url' size='32' value='' maxlength='96' placeholder='https://example.com'/>";
}
?>
                        </td>
                    </tr>
                    
                    <!-- Divider -->
                    <tr><td colspan='2'><hr/></td></tr>

                    <!-- Attendance Type -->
                    <tr>
                        <td>
                            <!-- TODO: make a helpfile for attendance type -->
                            <a href=''>Attendance Type*:</a>
                        </td>
                        <td>
                            <!-- TODO: move to helpfile -->
                            What kind of attendance do you expect for this event? 'In-person' means that you are
                            expecting participants to meet physically at a location. If your event also has online
                            functionality, please indicate such.  'Virtual' means you are expecting <i><b>all</b></i>
                            participants to meet via a virtual meeting space.
<?php
if(!empty($_SESSION['error']['attendance_type'])) {
    errormessage($_SESSION['error']['attendance_type']);
}
?>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
<?php
/* check if user has selected in-person */
if($_SESSION['form']['attendance_type'] == 'in-person') {
    echo "<input type='radio' name='attendance_type' value='in-person' checked />";
} else {
    echo "<input type='radio' name='attendance_type' value='in-person' />";
}
?>
                            In-Person&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
if($_SESSION['form']['has_online'] == true) {
    echo "<input type='checkbox' name='has_online' checked />";
} else {
    echo "<input type='checkbox' name='has_online' />";
}
?>
                            Streaming Available
                            <br />
<?php
/* check if user has selected virtual */
if($_SESSION['form']['attendance_type'] == 'virtual') {
    echo "<input type='radio' name='attendance_type' value='virtual' checked />";
} else {
    echo "<input type='radio' name='attendance_type' value='virtual' />";
}
?>
                            Virtual
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr><td colspan='2'><hr/></td></tr>

                    <!-- Location -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_location.php' target='_blank'>Location*:</a>
                        </td>
                        <td></td>
                    </tr>

                    <!-- TBD location -->
                    <tr>
                        <td></td>
                        <td>
<?php
if($_SESSION['form']['location_tbd'] == 'on') {
    echo "<input type='checkbox' name='location_tbd' checked />";
} else {
    echo "<input type='checkbox' name='location_tbd' />";
}
?>
                            <b>To Be Determined</b> &mdash; If you do not know where the event will take place,
                            check this box. An entry of 'TBD' will be used instead. This will override any city,
                            country, or region information you enter below.
                        </td>
                    </tr>

                    <!-- Country -->
                    <tr>
                        <td></td>
                        <td>
                            <b style='color: blue'>Country</b> &mdash; Identify the country where the event will be held, by entering the
                            two-letter ISO country code. Click <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_ISO.php' target='_blank'>
                            here</a> for a list of ISO country codes.
<?php
if(!empty($_SESSION['error']['country'])) {
    errormessage($_SESSION['error']['country']);
}
?>
                        </td>
                    </tr>


                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['country'])) {
    echo "<input type='text' name='country' maxlength = '2' size='2' value='" . $_SESSION['form']['country'] . "' /> <i> Two-letter ISO Country Code</i>\n";
} else {
    echo "<input type='text' name='country' maxlength = '2' size='2' value='' /> <i> Two-letter ISO Country Code</i>\n";
}
?>
                        </td>
                    </tr>

                    <!-- City -->
                    <tr>
                        <td></td>
                        <td>
                            <b style='color: blue'>City</b> &mdash; The name of the city where the event will take place:
<?php
if(!empty($_SESSION['error']['city'])) {
    errormessage($_SESSION['error']['city']);
}
?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['city'])) {
    echo "<input type='text' name='city' size='32' value='" . $_SESSION['form']['city'] . "' maxlength='96' />\n";
} else {
    echo "<input type='text' name='city' size='32' value='' maxlength='96' />\n";
}
?>
                        </td>
                    </tr>

                    <!-- Regions -->
                    <tr>
                        <td></td>
                        <td>
                            <b style='color: blue'>Regions</b> &mdash; Please indicate a region or regions for
                            this event. You must indicate a primary region. Click
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_region_codes.php' target='_blank'>here</a>
                            for a list of regions and countries ordered alphabetically.
<?php
if(!empty($_SESSION['error']['region'])) {
    errormessage($_SESSION['error']['region']);
}
?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
                            <select name='region1' size='1'>
                                <option value=''>[Primary Region]</option>
<?php
populate_regions('form', 'region1');
?>
                            </select>

                            <select name='region2' size='1'>
                                <option value=''>[Secondary Region]</option>
<?php
populate_regions('form', 'region2');
?>
                            </select>

                            <select name='region3' size='1'>
                                <option value=''>[Tertiary Region]</option>
<?php
populate_regions('form', 'region3');
?>
                            </select>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr><td colspan='2'><hr/></td></tr>
                    
                    <!-- Date -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_date.php' target='_blank'>Date*:</a>
                        </td>

                        <td>
                            Please specify the beginning and ending dates for this event. If you only have a month and
                            year at this time, just select the first month and year, and click the checkbox:
<?php
if(!empty($_SESSION['error']['date'])) {
    errormessage($_SESSION['error']['date']);
}
?>
                        </td>
                    </tr>

                    <!-- Start Date -->
                    <tr>
                        <td style='color: blue'>
                            &nbsp;&nbsp;&nbsp;Start Date &xrArr;
                        </td>

                        <td>
                            <select name='begin_month' size='1'>
                                <option value=''>[Month]</option>
<?php
populate_months('begin_month');
?>
                            </select>
                            
                            <select name='begin_year'>
                                <option value=''>[Year]</option>
<?php
populate_years('form', 'begin_year');
?>
                            </select>

                            &nbsp;&nbsp;<b>and</b>&nbsp;&nbsp;

                            <select name='begin_day'>
                                <option value=''>[Day]</option>
<?php
populate_days('begin_day');
?>
                            </select>
<?php
if($_SESSION['form']['date_tbd'] == 'on') {
    echo "<input type='checkbox' name='date_tbd' checked/>&nbsp;<b>Final dates to be determined</b>";
} else {
    echo "<input type='checkbox' name='date_tbd'/>&nbsp;<b>Final dates to be determined</b>";
}
?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style='color: blue'>
                            &nbsp;&nbsp;&nbsp;End Date &xrArr;
                        </td>

                        <td>
                            <select name='end_month'>
                                <option value=''>[Month]</option>
<?php
populate_months('end_month');
?>
                            </select>

                            <select name='end_year'>
                                <option value=''>[Year]</option>
<?php
populate_years('form', 'end_year');
?>
                            </select>

                            &nbsp;&nbsp;<b>and</b>&nbsp;&nbsp;

                            <select name='end_day'>
                                <option value=''>[Day]</option>
<?php
populate_days('end_day');
?>
                            </select>
                        </td>
                    </tr>

                    <!-- Languages -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/Register_language.php' target='_blank'>Language(s)*:</a>
                        </td>
                        <td>
                            If there is a single, primary language for this event, please specify this in the <i>Primary
                                Language</i> drop-down list. If the language is not listed, then please fill it in the
                            <i>Language not listed</i> box. If more than one language is used at this event, please
                            indicate that using the multiple drop-down lists. You must list at least one primary
                            language:
<?php
if(!empty($_SESSION['error']['language'])) {
    errormessage($_SESSION['error']['language']);
}
?>
                        </td>
                    </tr>

                    <!-- Primary Language -->
                    <tr>
                        <td></td>
                        <td>
                            <select name='language1'>
                                <option value=''>[Choose Language]</option>
<?php
populate_languages('language1');
?>
                            </select>
                            &nbsp;<i>Primary Language</i>
                        </td>
                    </tr>

                    <!-- Secondary Language -->
                    <tr>
                        <td></td>
                        <td>
                            <select name='language2'>
                                <option value=''>[Choose Language]</option>
<?php
populate_languages('language2');
?>
                            </select>
                            &nbsp;<i>Secondary Language</i>
                        </td>
                    </tr>

                    <!-- Additional Language -->
                    <tr>
                        <td></td>
                        <td>
                            <select name='language3'>
                                <option value=''>[Choose Language]</option>
<?php
populate_languages('language3');
?>
                            </select>
                            &nbsp;<i>Additional Language</i>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
<?php
if(!empty($_SESSION['form']['other_language'])) {
    echo "<input type='text' name='other_language' size='19' value='" . $_SESSION['form']['other_language'] . "' maxlength='254'/>";
} else {
    echo "<input type='text' name='other_language' size='19' value='' maxlength='254'/>";
}
?>
                            &nbsp;<i>Language not listed</i>
<?php
if(!empty($_SESSION['error']['other_language'])) {
    errormessage($_SESSION['error']['other_language']);
}
?>
                        </td>
                    </tr>
                </table>

                <br>

                <b style='font-size: 12pt; color: red'>Additional Information</b>
                <p>This additional information may be useful to better describe your network training event, or to allow
                other users to contact the event administrator if necessary.</p>
                
                <table style='width: 750px; min-width: 750px; margin: auto'>

                    <!-- Venue -->
                    <tr>
                        <td width='20%'>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_city.php' target='_blank'>Venue:</a>
                        </td>
                        <td>
                            The specific location (i.e. hotel, university, convention center) where the event will
                            take place within a country and a city:
<?php
if(!empty($_SESSION['error']['location'])) {
    errormessage($_SESSION['error']['location']);
}
?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['location'])) {
    echo "<input type='text' name='location' size='32' value='" . $_SESSION['form']['location'] . "' maxlength='96' />";
} else {
    echo "<input type='text' name='location' size='32' value='' maxlength='96' />";
}
?>
                        </td>
                    </tr>

                    <!-- Secondary URL -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_url_secondary.php' target='_blank'>Secondary URL:</a>
                        </td>
                        <td>
                            If there is more than one URL (example: event pages and organization pages) you can indicate a secondary URL here.
                            Make sure to include the 'http://' or the 'https://'.
<?php
if(!empty($_SESSION['error']['url_secondary'])) {
    errormessage($_SESSION['error']['url_secondary']);
}
?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
<?php
if(isset($_SESSION['form']['url_secondary'])) {
    echo "<input type='text' name='url_secondary' size='32' value='" . $_SESSION['form']['url_secondary'] . "' maxlength='96' placeholder='https://example.com' />";
} else {
    echo "<input type='text' name='url_secondary' size='32' value='' maxlength='96' placeholder='https://example.com' />";
}
?>
                        </td>
                    </tr>

                    <!-- Contact Information -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Contact.php' target='_blank'>Contact Person:</a>
                        </td>
                        
                        <td>
                            Please indicate a name and email address of someone who can be contacted concerning
                            administrative details of this event:
                        </td>
                    </tr>

                    <tr>
                        <td style='color: blue'>
                            &nbsp;&nbsp;&nbsp;Name &xrArr;
                        </td>

                        <td>
<?php
if(isset($_SESSION['form']['contact_name'])) {
    echo "<input type='text' name='contact_name' size='32' value='" . $_SESSION['form']['contact_name'] . "' maxlength='96' />";
} else {
    echo "<input type='text' name='contact_name' size='32' value='' maxlength='96' />";
}
?>
                        </td>
                    </tr>

                    <tr>
                        <td style='color: blue'>
                            &nbsp;&nbsp;&nbsp;Email &xrArr;
                        </td>
                        <td>
<?php
if(!empty($_SESSION['error']['contact_email'])) {
    errormessage($_SESSION['error']['contact_email'], true);
}
if(isset($_SESSION['form']['contact_email'])) {
    echo "<input type='text' name='contact_email' size='32' value='" . $_SESSION['form']['contact_email'] . "' maxlength='96' />";
} else {
    echo "<input type='text' name='contact_email' size='32' value='' maxlength='96' />";
}
?>
                        </td>
                    </tr>

                    <!-- Comments -->
                    <tr>
                        <td>
                            <a href='/<?=ROOT_DIR?>/helpfiles/WS_Create_comment.php' target='_blank'>Comments:</a>
                        </td>

                        <td>
                            Please write any additional comments here:
<?php
if(!empty($_SESSION['error']['text'])) {
    errormessage($_SESSION['error']['text']);
}
?>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
<?php
if(!empty($_SESSION['form']['text'])) {
    $comment_text = html_entity_decode(stripslashes($_SESSION['form']['text']));
    echo "<textarea rows=6 name='text' cols='36' wrap='hard'>" . $comment_text . "</textarea>";
} else {
    echo "<textarea rows=6 name='text' cols='36' wrap='hard'></textarea>";
}
?>
                        </td>
                    </tr>
                </table>

		<br>
<style>
#redButton{
background-color: red!important;
border-width: 2px!important;
border-radius: 4px!important;
float: right!important;
}
</style>

<?php
// BUTTONS
		// This is both the "Submit New Event" button for creating an event,
		// or the "Update Event" button for editing an event.
                global $FORM_TYPE;
                echo "<input type='submit' value='" . $FORM_TYPE . "' name='SUBMIT' />";

		// If we're on the "Update Event" page, also draw a delete button.
		$compare = "Update Event";
		if (strcmp($FORM_TYPE, $compare) == 0){
			echo "<input id='redButton' type='submit' value='Delete Event' name = 'SUBMIT' />";	
		}	

?>
            </td>
        </tr>
    </table>
	</form>
