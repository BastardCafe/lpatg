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
} else if (key_exists('gameid', $_GET)) {
} else {
  $dir= dirname($_SERVER['PHP_SELF']);
  header('Location: ' . $dir);
  die;
}
$gameid= 0;
if (key_exists('gameid', $_POST)) {
  $gameid= intval($_POST['gameid']);
}

$db= GetDB();
$query= "SELECT
  g.title,
  date_format(gp.gamedate, '%Y-%m-%d') as gamedate,
  p.name
FROM
  gameplay gp
  INNER JOIN player p ON gp.playerid=p.id
  INNER JOIN game g ON gp.gameid=g.id
";
if ($gameid > 0) {
  $query.=
"WHERE
  g.id = ?
";
}
$query.=
"ORDER BY
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

if ($gameid > 0) {
  header('Content-Type: application/json; charset=utf-8');
  print(json_encode($result));
} else {
  header('Content-Type: text/html; charset=utf-8');
  foreach($result as $gameplay) {
    echo 'Date: ' . $gameplay['gamedate'] . ' Game: ' . $gameplay['gametitle'] . ' by ' . $gameplay['playername'] . '<br/>';
  }
}
