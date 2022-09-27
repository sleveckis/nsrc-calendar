<?php
function finalize_event() {

    draw_nsrc_header();

    /* DB Structure */
    /* 
        +-------------------+---------------------+------+-----+---------------------+-----------------------------+
        | Field             | Type                | Null | Key | Default             | Extra                       |
        +-------------------+---------------------+------+-----+---------------------+-----------------------------+
        | id                | bigint(20) unsigned | NO   | PRI | NULL                | auto_increment              |
        | user_id           | bigint(20) unsigned | NO   |     | 0                   |                             |
        | title             | varchar(255)        | NO   |     |                     |                             |
        | date_begin        | date                | YES  |     | NULL                |                             |
        | date_end          | date                | YES  |     | NULL                |                             |
        | url               | varchar(255)        | NO   |     |                     |                             |
        | url_secondary     | varchar(255)        | YES  |     | NULL                |                             |
        | country           | char(2)             | NO   |     |                     |                             |
        | city              | varchar(96)         | NO   |     |                     |                             |
        | region            | varchar(96)         | NO   |     |                     |                             |
        | region_secondary  | varchar(96)         | YES  |     | NULL                |                             |
        | region_terciary   | varchar(96)         | YES  |     | NULL                |                             |
        | language1         | varchar(255)        | YES  |     | NULL                |                             |
        | language2         | varchar(255)        | YES  |     | NULL                |                             |
        | language3         | varchar(255)        | YES  |     | NULL                |                             |
        | other_language    | varchar(255)        | YES  |     | NULL                |                             |
        | location          | varchar(255)        | YES  |     | NULL                |                             |
        | contact_name      | varchar(255)        | YES  |     | NULL                |                             |
        | contact_email     | varchar(255)        | YES  |     | NULL                |                             |
        | comment           | text                | YES  |     | NULL                |                             |
        | year              | year(4)             | NO   |     | 0000                |                             |
        | month             | varchar(96)         | NO   |     |                     |                             |
        | approved          | char(1)             | NO   |     | 0                   |                             |
        | directory         | varchar(255)        | NO   |     |                     |                             |
        | creation_date     | timestamp           | NO   |     | CURRENT_TIMESTAMP   | on update CURRENT_TIMESTAMP |
        | creation_user     | varchar(96)         | YES  |     | NULL                |                             |
        | last_update       | datetime            | NO   |     | 0000-00-00 00:00:00 |                             |
        | update_user       | varchar(96)         | YES  |     | NULL                |                             |
        | deleted           | enum('Y','N')       | YES  |     | N                   |                             |
        | workshop_tutorial | char(3)             | YES  |     | NULL                |                             |
        | date_tbd          | char(3)             | YES  |     | NULL                |                             |
        | location_tbd      | char(3)             | YES  |     | NULL                |                             |
        | streaming         | tinyint(1)          | YES  |     | 0                   |                             |
        | remote            | tinyint(1)          | YES  |     | 0                   |                             |
        +-------------------+---------------------+------+-----+---------------------+-----------------------------+
     */

    /* first, get user information */
    if(!isset($_SESSION['authenticated_user'])) {
        die('Session is not active. Unexpected error. Please contact calendar@nsrc.org for help.');
    }

    $values = get_form_values_array();
    $query = get_finalize_query();

    $result = db_insert($query, $values);
    if(!$result[0]) {
        echo "<table class='info_table'>";
        echo "  <tr>";
        echo "    <td>";
        echo "      <b style='text-align: center'>An error occured while creating the database entry for your event. Please contact calendar@nsrc.org for help.</b>";
        echo "    </td>";
        echo "  </tr>";
        echo "  <tr>";
        echo "    <td>";
        echo "      The error that occured was:<br>";
        echo $result[1];
        echo "    </td>";
        echo "  </tr>";
        echo "</table>";
    } else {
        $row = db_fetch1("select * from workshop where id=?", array($_SESSION['eventid']));
        echo "<table class='info_table'>";
        echo "  <tr>";
        echo "    <td>";
        echo "      <b>Thank you for your network event entry!</b>";
        echo "      <br />";
        if($_SESSION['is_update']) {
            echo "      If you'd like you may <a href=''>Update or Delete another Network Event</a>, or you can view the <a href='/";
            echo ROOT_DIR;
            echo "/index.php'>calendar of Events</a>.";
        }
        else {
            echo "      If you'd like you may <a href=''>Create another Network Event</a>, or you can view the <a href='/";
            echo ROOT_DIR;
            echo "/index.php'>calendar of Events</a>.";
        }
        echo "    </td>";
        echo "  </tr>";
        echo "</table>";
        echo "<br />";
        echo "<table class='info_table'>";
        echo "  <tr>";
        echo "    <td><a href='/" . ROOT_DIR . "/helpfiles/workshop_info.php?id=" . $row['id'] . "' target='_blank'>" . $row['title'] . "</a></td>";
        echo "    <td>" . $row['month'] . ", " . $row['year'] . "</td>";
        if($location_tbd == 'on')
            echo "    <td>TBD (To Be Determined)</td>";
        else {
            $city = htmlentities($row['city'], ENT_QUOTES);
            $country = htmlentities($row['country'], ENT_QUOTES);
            echo "<td>";
            if($city != '') {
                echo $city;
                if($country != '')
                    echo ", ";
            }
            if($country != '') {
                if($country == 'ZZ')
                    echo "(Virtual)";
                else
                    echo $country;
            }
            echo "</td>";
        }
        echo "  </tr>";
        echo "</table>";
    }

    unset($_SESSION['form']);
    unset($_SESSION['error']);
    unset($_SESSION['search']);

    draw_nsrc_footer();
}
?>
