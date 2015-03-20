<?php
require 'config.php';

use Facebook\FacebookRedirectLoginHelper;

unset($_SESSION['facebook']);

$helper = new FacebookRedirectLoginHelper($redirectUrl);
$loginUrl = $helper->getLoginUrl(array('scope' => 'public_profile,user_friends,read_friendlists,user_groups'));

header('Location: ' . $loginUrl);
?>