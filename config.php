<?php
require __DIR__ . '/vendor/autoload.php';

use Facebook\FacebookSession;

session_start();

$appId = 'YOUR_APP_ID';
$appSecret = 'YOUR_APP_SECRET';
$redirectUrl = 'http://your.redirect.url/callback.php';

FacebookSession::setDefaultApplication($appId, $appSecret);
?>