<?php
function populate_search_results($results) {
    echo "<b style='color: blue'>Query Results:</b>";
    echo "<table class='info_table'>";
    echo "    <tr>";
    echo "        <th>Title</th>";
    echo "        <th>Date</th>";
    echo "        <th>Location</th>";
    echo "    </tr>";

    $i = 0;
    while($row = $results->fetch()) {
        $i++;
        echo "<tr>";
        echo "<td><a href='/" . ROOT_DIR . "/helpfiles/workshop_info.php?id=" . $row['id'] . "' target='_blank'>" . $row['title'] . "</a></td>";
        if($row['date_tbd'] == 'on') {
            echo "<td>" . $row['month'] . ", " . $row['year'] . " (TBD)</td>";
        } else {
            echo "<td>" . $row['month'] . ", " . $row['year'] . "</td>";
        }

        //TODO: formatting for virtual, in-person, in-person w/ online -- Get NSRC opinion
        echo "<td>" . $row['city'] . ", " . country_lookup($_row_name_nw['country']) . "</td>";
        echo "</tr>\n";
    }

    if(empty(row['title']) && $i == 0) {
        echo "<tr><td colspan=3><b style='color: red'>No Workshops Found</b></td></tr>";
    }

    echo "</table>";
}
?>


<table class='search_table'>
    <tr>
        <td>
            <b style='font-size: 12pt'>Search for an event:</b>
            <br>
            You can search by year, region, or a substring of the event name. You can search by one, or all of these as
            you wish. If you search by region this will check for primary, secondary, and tertiary regions listed for
            the event.
<?php
$row_name_nw = db_fetch1('select count(*) from workshop', array());
$num_workshops = $row_name_nw['count(*)'];
?>
            <br>
            <br>
            <hr>
            <form method='POST' action=''>
                <br/>
                <input type='submit' value='View All' name='SUBMIT'/> See all available events.
                <span style='color: blue'>Current event count is <?=$num_workshops?></span>
            </form>

            <br/>
            <hr>
            
            <form method='POST' action=''>
                <br/>
                <select name='by_date'>
                    <option value=''>[Search by Year]</option>
<?php
populate_years('search', 'by_date');
?>
                </select>

                <select name='by_region'>
                    <option value=''>[Search by Region]</option>
<?php
populate_regions('search', 'by_region');
?>
                </select>
                
                <b style='margin-left: 10px'>By Name: </b>
<?php
if(isset($_SESSION['search']['substring'])) {
    echo "<input type='text' name='substring' size='15' value='" . $_SESSION['search.substring'] . "' maxlength='254' />";
} else {
    echo "<input type='text' name='substring' size='15' value='' maxlength='254' />";
}
?>
            <br><br>
            <input type='submit' value='Search For Event' name='SUBMIT'>
<?php
if(($_SESSION['do_search'] == true) &&
   (empty($_SESSION['search']['by_date'])) &&
   (empty($_SESSION['search']['by_region'])) &&
   (empty($_SESSION['search']['substring']))) {

    echo "<b style='color: red'>You have not chosen anything to search by!</b>";
    $_SESSION['empty_search'] = true;
} else {
    $_SESSION['empty_search'] = false;
}
?>
            </form>
        </td>
    </tr>
</table>

<?php
if($_SESSION['view_all'] == true) {
    $result_name_nw = db_exec('select * from workshop order by year desc', array());
    populate_search_results($result_name_nw);
}

elseif($_SESSION['do_search'] == true && $_SESSION['empty_search'] == false) {
    $query_sth = workshop_lookup($_SESSION['search']['by_date'],
        $_SESSION['search']['by_region'],
        $_SESSION['search']['substring']);
    
    populate_search_results($query_sth);
}
?>
