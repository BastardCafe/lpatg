<?php
/* Put the following variables in config-local.inc.php to setup
 * database connection and Facebook SDK.
 * - $dbConfig - configure user, host etc.
 * - $facebookConfig - configure Facebook app ID etc.
 * - $graphIncludePath - location of Facebook SDK
 */
$dbConfig = array(
  'user' => '',
  'host' => '',
  'database' => '',
  'passowrd' => ''
);
$facebookConfig = array(
  'app_id' => '',
  'app_secret' => '',
  'default_graph_version' => ''
);
$graphIncludePath = '';

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config-local.inc.php');
require_once('db.inc.php');
require_once('facebook.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');
require_once($graphIncludePath . DIRECTORY_SEPARATOR . 'src/Facebook/autoload.php');
