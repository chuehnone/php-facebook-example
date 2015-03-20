<?php
require 'config.php';

use Facebook\FacebookRedirectLoginHelper;

unset($_SESSION['facebook']);

$helper = new FacebookRedirectLoginHelper($redirectUrl);
$loginUrl = $helper->getLoginUrl(array('scope' => 'public_profile,user_friends,user_groups,read_stream'));

header('Location: ' . $loginUrl);
?>