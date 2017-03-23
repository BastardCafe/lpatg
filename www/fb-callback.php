<?php
session_start();
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'config.inc.php');

$fb= GetFacebook();
$helper= $fb->getRedirectLoginHelper();

try {
  $accessToken= $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  print('Graph returned an error: ' . $e->getMessage());
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  print('Facebook SDK returned an error: ' . $e->getMessage());
  exit;
}

if (!isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    print('Error: ' . $helper->getError() . PHP_EOL);
    print('Error Code: ' . $helper->getErrorCode() . PHP_EOL);
    print('Error Reason: ' . $helper->getErrorReason() . PHP_EOL);
    print('Error Description: ' . $helper->getErrorDescription() . PHP_EOL);
  } else {
    header('HTTP/1.0 400 Bad Request');
    print('Bad request');
  }
  exit;
}


$oAuth2Client = $fb->getOAuth2Client();
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId('620966284777812');
$tokenMetadata->validateExpiration();
if (! $accessToken->isLongLived()) {
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    print('<p>Error getting long-lived access token: ' . $helper->getMessage() . '</p>' . PHP_EOL . PHP_EOL);
    exit;
  }
}

$response= $fb->get('/me?fields=id,name', (string)$accessToken);
$user= $response->getGraphUser();
if ($user) {
  $_SESSION['accesstoken']= (string)$accessToken;

  $db= GetDB();
  $query= "INSERT IGNORE INTO player (facebookid, name) VALUES (?,?)";
  $stmt= $db->prepare($query);
  $stmt->bind_param('ss', $facebookid, $name);
  $facebookid= $user['id'];
  $name= $user['name'];
  $stmt->execute();
  $stmt->close();
  $db->close();
}

$dir= dirname($_SERVER['REQUEST_URI']);
header('Location: '.$dir);
