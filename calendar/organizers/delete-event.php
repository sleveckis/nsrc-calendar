<table class="search_table">
    <tr>
        <td>
            <b style='font-size: 12pt'>Update a Network Training Event: Step 2</b>
            <br>
            <p>You have chosen to update the network training event currently named: "<b style='color: blue'>
            <?=$_SESSION['form']['title']?></b>".
<?php
$action = available_action($_SESSION['eventid'], $userid);
if($action == 'Update or Delete') {
?>
            This event can be deleted. If you wish to do this, press the "Delete Event" button below. Otherwise, make
            updates to the event information shown below and then press "Update Event". You can choose "Go Back" if you
            wish to pick a different network event.
            </p>
            <form action='' method='POST'>
                <input type='submit' value='Delete Event' name='SUBMIT'/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type='submit' value='Go Back' name='SUBMIT' />
            </form>
<?php
} else {
?>
            This event can only be updated. If you wish to do this, make updates to the event information shown below
            and then press "Update Event". You can choose "Go Back" if you wish to pick a different network event.
            </p>
            <form action='' method='POST'>
                <input type='submit' value='Go Back' name='SUBMIT' />
            </form>
<?php
}
?>          
        </td>
    </tr>
</table>
