<?php
session_start();

session_destroy();
$dir= dirname($_SERVER['REQUEST_URI']);
header('Location: '.$dir);
