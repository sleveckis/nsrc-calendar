<?php

/* first check the privs of the user */
$row = db_fetch1('select * from user where userid=?', array($_SESSION['authenticated_user']));
$username = $row['userid'];
$userid = $row['id'];
$privs = $row['privilege'];

$is_admin = false;
if($privs == '1' or $privs == '2')
    $is_admin = true;

if($is_admin) {
    $row_upcoming = db_fetch1('select count(*) from workshop where deleted="N" and NOW() <= date_end', array());
    $num_upcoming = $row_upcoming['count(*)'];

    $row_all = db_fetch1('select count(*) from workshop where deleted="N"', array());
    $num_all = $row_all['count(*)'];

    $_SESSION['admin'] = array(
        "num_upcoming" => $num_upcoming,
        "num_all" => $num_all
    );

}

draw_update_rules($is_admin);
unset($_SESSION['admin']);
?>
<br>
<form action='' method='POST'>

<?php

if($is_admin) {
    if($_SESSION['all_events'] == false) {
        $event_count = db_fetch1('select count(*) from workshop where deleted="N" and NOW() < date_end order by date_begin asc', array())['count(*)'];
        $result = db_exec('select * from workshop where deleted="N" and NOW() <= date_end order by date_begin asc', array());
    } else {
        $event_count = db_fetch1('select count(*) from workshop where deleted="N" order by date_begin asc', array())['count(*)'];
        $result = db_exec('select * from workshop where deleted="N" order by date_begin asc', array());
    }
} else {
    $event_count = db_fetch1('select count(*) from workshop where deleted="N" and user_id=? order by date_begin asc', array($userid))['count(*)'];
    $result = db_exec('select * from workshop where deleted="N" and user_id=? order by date_begin asc', array($userid));
}

draw_update_key($event_count, $username);


$to_update = 0;
?>


    <table class="form_table">
        <tr>
            <th style='width: 140px; text-align: center'>Available Action(s)</th>
            <th style='width: 380px; text-align: center'>Title</th>
            <th style='width: 140px; text-align: center'>Date</th>
            <th style='width: 140px; text-align: center'>Location</th>
        </tr>
<?php

foreach($result->fetchAll() as $row) {
    echo "<tr>";

    $action = available_action($row['id'], $userid);

    if($action == 'Update' or $action == 'Update or Delete') {
        echo "<td><input type='radio' name='available' value='" . $row['id'] . "'/>&nbsp;" . $action . "</td>";
        $to_update += 1;
    } else {
        echo "<td>" . $action . "</td>";
    }

    echo "<td><a href='/" . ROOT_DIR . "/helpfiles/workshop_info.php?id=" . $row['id'] . "' target='_blank'>" . $row['title'] ."</a></td>";

    if($row['date_tbd'] == 'on') {
        echo "<td>TBD</td>";
    } else {
        echo "<td>";
        echo $row['month'];
        echo ", ";
        echo $row['year'];
        echo "</td>";
    }

    if($row['location_tbd'] == 'on') {
        echo "<td>TBD</td>";
    } else {
        echo "<td>";

        if($row['city'] != '') {
            echo (string) $row['city'];
            if($row['country'] != '')
                echo ", ";
        }

        if($row['country'] != '') {
            if($row['country'] == 'ZZ')
                echo "(Virtual)";
            else echo (string) $row['country'];
        }

        echo "</td>";
    }

    echo "</tr>";
}
echo "</table><br>";

draw_update_key($event_count, $username);

?>
</form>
