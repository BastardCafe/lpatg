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

$playerName= $user['name'];
ob_start();

$headTitle= 'YAAIFO:LPATG';
$page= '<div data-role="page" id="gamelist" data-theme="a" data-content-theme="a">
  <div data-role="header" data-position="fixed">
    <h1>' . $headTitle . '</h1>
  </div>
  <div data-role="content">
';

$db= GetDB();

$query= "SELECT * FROM game WHERE id NOT IN (SELECT gameid FROM gameplay) ORDER BY title ASC";
$dbres= $db->query($query);
if ($dbres) {
  /*
  $page.='<div data-role="fieldcontain"><label for="playedselect">Show played games</label>
    <select name="playedselect" id="playedselect" data-role="slider">
      <option value="no"></option>
      <option value="yes"></option>
    </select></div>' . PHP_EOL;
   */
  $page.='<ul data-role="listview" data-inset="true" data-autodividers="true" data-filter="true">' . PHP_EOL;
  while ($row= $dbres->fetch_assoc()) {
    $page.= '<li><a href="#" onClick="SetGameData('.$row['id'].','.$row['bbgid'].');">'.$row['title'].'</a></li>' . PHP_EOL;
  }
  $page.='</ul>' . PHP_EOL;
}

$page.='
  </div>
</div>

<div data-role="page" data-dialog="true" id="gameplayed">
  <div data-role="main" class="ui-content">
    <h2 name="gametitle" id="gametitle"></h2>
    <form method="post" action="gameplayed.php">
      <h3>Player name: '.$user['name'].'</h3>
<div>
      <input type="hidden" name="gamedate" id="gamedate" data-role="date" data-inline="true" />
</div>
      <input type="hidden" name="gameid" id="gameid" />
      <input type="hidden" name="bggid" id="bggid" />
      <input type="submit" value="Submit" data-icon="check" data-iconpos="right" />
    </form>
  </div>
  <div data-role="collapsible" id="previousplaysheader">
    <h2>Previous plays ...</h2>
    <ul data-role="listview" data-filter="false" name="previousplays" id="previousplays" class="ui-content">
      <li>Test ...</li>
    </ul>
  </div>
  <div data-role="footer">
    <a href="#" class="ui-btn" data-rel="back">Back</a>
  </div>
</div>
<script>
function SetGameData(gameId, bggId) {
  $("#gameid").val(gameId);
  $("#bggid").val(bggId);
  $.ajax({
    type: "POST",
    url: "gametitle.php",
    data: "gameid="+gameId,
    success: function(data) { $("#gametitle").text(data); $.mobile.navigate("#gameplayed"); }
  });
  $.ajax({
    type: "POST",
    url: "gameplays.php",
    data: "gameid="+gameId,
    success: function(data) {
      $("#previousplays").empty();
      if (data.length > 0) {
        $("#previousplaysheader").show();
      } else {
        $("#previousplaysheader").hide();
      }
      $.each(data, function(i, item) { 
$("#previousplays").append(\'<li><span class="ui-content">\' + item.gamedate + \' by \' + item.playername + \'</span></li>\');
      });
    }
  });
}
$(document).ready(function() {
  $.datepicker.setDefaults({
    dateFormat: "yy-mm-dd",
    minDate: "2017-03-01",
    autoSize: true
  });
  $("#playername").textinput("disable");
});
</script>
';
$header= GetHeader();
$footer= GetFooter();

print($header . $page . $footer);

ob_get_contents();
ob_end_flush();
