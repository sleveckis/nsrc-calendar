<?php

// connect.php
//
// Hervey Allen for ISOC, Summer 2003
//

//$my_dbh = new PDO('mysql:host=db.nsrc.org;dbname=calendar', "calendar", "HamsterMcLarenDeBeersDucks");

// This is our new db vm and container and the requirement to use ssl for connecting.
// HA - 21 Oct 2020


$my_dbh = new PDO(
    'mysql:host=localhost;dbname=calendar_copy',
    'calendar',
    'Unneuddo#01',
);

function db_fetch1($query, $params) {
  global $my_dbh;
  $sth = $my_dbh->prepare($query);
  $sth->execute($params);
  return $sth->fetch();
}

function db_fetch_all($query, $params) {
  global $my_dbh;
  $sth = $my_dbh->prepare($query);
  $sth->execute($params);
  return $sth->fetchAll();
}

function db_exec($query, $params) {
  global $my_dbh;
  $sth = $my_dbh->prepare($query);
  $sth->execute($params);
  return $sth;
}

function db_update($query, $params) {
  global $my_dbh;
  $sth = $my_dbh->prepare($query);
  if ($sth->execute($params)) {
    return array(TRUE, "No error");
  } else {
    $ei = $sth->errorInfo();
    return array(FALSE, $ei[2]);
  }
}

function db_insert($query, $params) {
  return db_update($query, $params); // no difference with update for our purposes
}

function db_delete($query, $params) {
  return db_update($query, $params); // no difference with update for our purposes
}

?>
