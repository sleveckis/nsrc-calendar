<table class="info_table">
    <tr>
        <td>
            <b style='font-size: 12pt'>Verify that the information you entered is correct.</b>
            <ul>
                <li>If it is, click <b>Finalize Event.</b></li>
                <li>Otherwise, click <b>Go Back</b> and make the appropriate changes.</li>
            </ul>
        </td>
    </tr>
</table>

<br>

<form method='POST' action=''>
    <table class="info_table">
        <tr>
            <td style='width: 25%'>Event Title:</td>
            <td>
<?php
form_value('title');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Event URL:</td>
            <td>
<?php
form_value('url');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Attendance Type:</td>
            <td>
<?php
display_attendance_type();
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>City, Country:</td>
            <td>
<?php
display_city_country();
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Region(s):</td>
            <td>
<?php
display_regions();
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Start Date:</td>
            <td>
<?php
display_begin_date();
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>End Date:</td>
            <td>
<?php
display_end_date();
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Primary Language:</td>
            <td>
<?php
form_value('language1');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Secondary Language:</td>
            <td>
<?php
form_value('language2');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Tertiary Language:</td>
            <td>
<?php
form_value('language3');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Other Language:</td>
            <td>
<?php
form_value('other_language');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Venue:</td>
            <td>
<?php
form_value('location');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Secondary URL:</td>
            <td>
<?php
form_value('url_secondary');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Contact Name:</td>
            <td>
<?php
form_value('contact_name');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Contact Email:</td>
            <td>
<?php
form_value('contact_email');
?>
            </td>
        </tr>
        <tr>
            <td style='width: 25%'>Comments:</td>
            <td>
<?php
form_value('text');
?>
            </td>
        </tr>

        <tr>
            <td colspan='2' style='text-align: center'>
<?php
                global $CONFIRM_TYPE;
                echo "<input type='submit' value='" . $CONFIRM_TYPE . "' name='SUBMIT'>";
?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value='Finalize Event' name='SUBMIT' />
            </td>
        </tr>
    </table>
</form>
