<?php
function GetFacebook() {
  global $facebookConfig;
  return new Facebook\Facebook($facebookConfig);
}

