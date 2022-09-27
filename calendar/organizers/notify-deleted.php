<table class="info_table">
    <tr>
        <td>
            <center><b style='color: red; font-size: 12pt;'>Notice</b></center>
            <hr />
<?php
if(empty($_SESSION['error']['delete'])) {
?>
            Event "<b style='color: blue'><?=$_SESSION['form']['title']?></b>" has been deleted.
<?php
} else {
    errormessage($_SESSION['error']['delete']);
}
?>
        </td>
    </tr>
</table>
