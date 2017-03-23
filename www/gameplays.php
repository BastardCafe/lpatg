<?php
session_start();
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'config.inc.php');

if (key_exists('accesstoken', $_SESSION)) {
  try {
    $fb= GetFacebook();
  } catch(Exception $e) {
    die('Exception caught: ' . $e->getMessage());
  }
  $accessToken= $_SESSION['accesstoken'];
  $response= $fb->get('/me?fields=id,name', $accessToken);
  $user= $response->getGraphUser();
} else {
  $dir= dirname($_SERVER['PHP_SELF']);
  die('Unauthorized');
//  header('Location: '.$dir);
  exit;
}
$gameid= intval($_POST['gameid']);
/*
if (!key_exists('gameid', $_POST)) {
  if (!key_exists('gameid', $_GET)) {
    $dir= dirname($_SERVER['PHP_SELF']);
    header('Location: '.$dir);
    exit;
  } else {
    $gameid= intval($_GET['gameid']);
  }
} else {
  $gameid= intval($_POST['gameid']);
}
 */
$db= GetDB();
$query= "SELECT
  g.title,
  date_format(gp.gamedate, '%Y-%m-%d') as gamedate,
  p.name
FROM
  gameplay gp
  INNER JOIN player p ON gp.playerid=p.id
  INNER JOIN game g ON gp.gameid=g.id
WHERE
  g.id = ?
ORDER BY
  gp.gamedate DESC,
  p.name ASC";

$result= array();
$stmt= $db->prepare($query);
if ($stmt) {
  $stmt->bind_param('i', $gameid);
  if ($stmt->execute()) {
    $stmt->bind_result($gameTitle, $gameDate, $playerName);
    while ($stmt->fetch()) {
      array_push($result, array("gametitle" => $gameTitle, "gamedate" => $gameDate, "playername" => $playerName));
    }
  }
}

header('Content-Type: application/json; charset=utf-8');
print(json_encode($result));
