<?php
function GetHeader() {
  global $headTitle, $pageTitle, $style;
  return('
<!DOCTYPE html>
<html>
<head>
<title>' . $headTitle . '</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<link href="css/jtsage-datebox-4.1.1.jqm.min.css" rel="stylesheet" type="text/css">
<script
  src="https://code.jquery.com/jquery-1.12.4.min.js"
  integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
  crossorigin="anonymous"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<script src="js/jtsage-datebox-4.1.1.jqm.min.js" type="text/javascript"></script>
' . $style . '
</head>
<body>
');
}
