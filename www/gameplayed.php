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
  $dir= dirname($_SERVER['REQUEST_URI']);
  header('Location: '.$dir);
  exit;
}

$requiredKeys= array('gameid', 'bggid', 'gamedate');
foreach ($requiredKeys as $key) {
  if (!key_exists($key, $_POST)) {
    header('Location: .');
    die;
  }
}
$gameid= $_POST['gameid'];
$bggid= $_POST['bggid'];
$gamedate= $_POST['gamedate'];
$playername= $user['name'];

$db= GetDB();
$query= "SELECT id FROM player WHERE facebookid = ?";
$stmt= $db->prepare($query);
$stmt->bind_param('s', $facebookid);
$facebookid= $user['id'];

$stmt->execute();
$stmt->bind_result($playerid);
$stmt->fetch();
$stmt->close();

$query= "INSERT INTO gameplay (gameid, playerid, gamedate) VALUES (?,?,?)";
$stmt2= $db->prepare($query);
if (!$stmt2) {
  error_log("DB Error: " . $db->error);
}
$stmt2->bind_param('iis', $gameid, $playerid, $gamedate);
$stmt2->execute();

$query= "SELECT title FROM game WHERE id = ?";
$stmt3= $db->prepare($query);
if ($stmt3) {
  $stmt3->bind_param('i', $gameid);
  if ($stmt3->execute()) {
    $stmt3->bind_result($gameTitle);
    $stmt3->fetch();
  }
}
ob_start();

$headTitle= 'YAAIFO:LPATG';
$page= '<div data-role="page" id="gamelist">
  <div data-role="header" data-position="fixed">
    <h1>' . $headTitle . '</h1>
  </div>
  <div role="content">
';
$page.='<h2 class="ui-content">Thank you!!!</h2>
    <p class="ui-content">We have registered that you have played one of our games. Well done!</p>
    <div class="ui-content">
      <div class="ui-grid-a">
        <div class="ui-block-a" style="width: 20%;"><span>Game</span></div>
        <div class="ui-block-b" style="width: 80%;"><span>' . $gameTitle . '</span></div>
        <div class="ui-block-a" style="width: 20%;"><span>Player</span></div>
        <div class="ui-block-b" style="width: 80%;"><span>' . $playername . '</span></div>
        <div class="ui-block-a" style="width: 20%;"><span>Date</span></div>
        <div class="ui-block-b" style="width: 80%;"><span>' . $gamedate . '</span></div>
      </div>
    </div>
    <a href="gamelist.php" class="ui-btn">Back</a>
  </div>
</div>
';

$header= GetHeader();
$footer= GetFooter();

print($header . $page . $footer);

ob_get_contents();
ob_end_flush();
