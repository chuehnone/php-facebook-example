<?php
require __DIR__ . '/vendor/autoload.php';

use Facebook\FacebookSession;

session_start();

FacebookSession::setDefaultApplication('YOUR_APP_ID', 'YOUR_APP_SECRET');

$redirectUrl = 'http://your.redirect.url';
?>