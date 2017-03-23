<?php
session_start();
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'config.inc.php');

try {
  $fb= GetFacebook();
} catch(Exception $e) {
  die('Exception caught: ' . $e->getMessage());
}

if (key_exists('accesstoken', $_SESSION)) {
  $accessToken= $_SESSION['accesstoken'];
  $response= $fb->get('/me?fields=id,name', $accessToken);
  $user= $response->getGraphUser();
  if ($user) {
    header('Location: gamelist.php');
    exit;
  }
}

$headTitle= 'YAAIFO: LPATG';
$page= '<div data-role="page">
  <div data-role="header" data-position="fixed">
    <h1>' . $headTitle . '</h1>
  </div>
  <div role="content">
';

$helper= $fb->getRedirectLoginHelper();
$protocol= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
$site= $_SERVER['SERVER_NAME'];
$dir= dirname($_SERVER['PHP_SELF']);
$callbackUrl= $protocol.'://'.$site.$dir.'/fb-callback.php';
$loginUrl= $helper->getLoginUrl($callbackUrl);
$page.='<h2 class="ui-content">Please login</h2>
  <p class="ui-content">This site is meant for members of the Bastard Café Guru group. Please login with Facebook to continue.</p>
    <a class="ui-btn" href="'.htmlspecialchars($loginUrl).'">Login with Facebook</a><br/>
  </div>
</div>' . PHP_EOL;
$header= GetHeader();
$footer= GetFooter();

print($header . $page . $footer);
