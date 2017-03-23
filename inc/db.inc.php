<?php
function GetDB() {
  global $dbConfig;
  $dbuser= $dbConfig['user'];
  $dbhost= $dbConfig['host'];
  $dbname= $dbConfig['database'];
  $dbpass= $dbConfig['password'];
  $db= new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($db->connect_error) {
    header('Location: error.php');
    error_log('Bastard Connect Error(' . $db->connect_errno . ')' . $db->connect_error);
    die;
  }
  $db->set_charset('utf8');
  return ($db);
}

