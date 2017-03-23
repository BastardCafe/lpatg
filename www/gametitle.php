<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'config.inc.php');

$db= GetDB();
$query="SELECT title FROM game WHERE id=?";
$stmt= $db->prepare($query);
if ($stmt) {
  $stmt->bind_param('i', $gameid);
  $gameid= $_POST['gameid'];
  if ($stmt->execute()) {
    $stmt->bind_result($gametitle);
    if ($stmt->fetch()) {
      print($gametitle);
    }
  }
  $stmt->close();
}
$db->close();
