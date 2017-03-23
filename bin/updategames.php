<?php
$time= -microtime(true);
header('Content-Type: text/plain');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'config.inc.php');

print('-------------------------' . PHP_EOL);
if ((count($argv) > 1) && ($argv[1] == 'c')) {
  print('Reading from BBG' . PHP_EOL);
  $curl= curl_init();
  curl_setopt($curl, CURLOPT_URL, 'https://www.boardgamegeek.com/xmlapi2/collection/?username=bastardcafe&own=1&subtype=boardgame&excludesubtype=boardgameexpansion');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
  $gamelist= curl_exec($curl);
  curl_close($curl);
} else {
  print('Reading from local file' . PHP_EOL);
  $gamelist= file_get_contents('../gamelist.xml');
}

$document= simplexml_load_string($gamelist);
if ($gamelist === false) {
  echo "Failed loading XML:";
  die;
}

$db= GetDB();

$query= 'INSERT IGNORE INTO game (bbgid, title, location) VALUES (?,?,?)';
$stmt= $db->prepare($query);
if ($stmt === false) {
  die('Unable to prepare INSERT INTO statement'.PHP_EOL);
}
$stmt->bind_param('iss', $bbgid, $title, $location);
$count= 0;
$newGames= 0;
$skippedGames= 0;
$errors= 0;
foreach($document as $item) {
  $count++;
  $bbgid= $item['objectid'];
  $title= $item->name;
  $location= substr($item->comment, 2);
  $stmt->execute();
  if ($stmt->errno) {
    print('Unable to handle game: ');
    print('ID:' . $item['objectid'] . ',name:' . $item->name . PHP_EOL);
    $errors++;
  } else {
    if ($db->insert_id) {
      $newGames++;
      print('Added game: ');
      print('ID:' . $item['objectid'] . ',name:' . $item->name . PHP_EOL);
    }
  }
}
$stmt->close();
$db->close();
$time+= microtime(true);
print('Items in XML: ' . $document['totalitems'] . PHP_EOL);
print('Handled ' . $count . ' games' . PHP_EOL);
print('New games: ' . $newGames . PHP_EOL);
print('Errors: ' . $errors . PHP_EOL);
print('Elapsed time: ' . sprintf('%f', $time) . PHP_EOL);
print('-------------------------' . PHP_EOL);
