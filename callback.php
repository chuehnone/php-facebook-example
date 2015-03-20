<?php
require 'config.php';

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;

$helper = new FacebookRedirectLoginHelper($redirectUrl);
$session = null;
if (isset($_SESSION['facebook'])) {
    $session = $_SESSION['facebook'];
} else {
    try {
        $session = $helper->getSessionFromRedirect();
    } catch(FacebookRequestException $ex) {
        // When Facebook returns an error
        var_dump($ex);
    } catch(Exception $ex) {
        // When validation fails or other local issues
        var_dump($ex);
    }
}

if ($session) {
    $_SESSION['facebook'] = $session;

    // Get personal info
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $me = $graphObject->asArray();
    formatMe($me);

    // Get personal friends
    $request = new FacebookRequest($session, 'GET', '/me/taggable_friends');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $friends = $graphObject->asArray();
    foreach ($friends['data'] as $friend) {
        formatFriend($friend);
    }
    echo '<br />';

    // Get personal groups
    $request = new FacebookRequest($session, 'GET', '/me/groups');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $groups = $graphObject->asArray();
    foreach ($groups['data'] as $group) {
        formatGroup($group);
    }
    echo '<br />';

    // Get personal feeds
    // $request = new FacebookRequest($session, 'GET', '/me/feed');
    // $response = $request->execute();
    // $graphObject = $response->getGraphObject();
    // var_dump($graphObject);
}

function formatMe($ary) {
    $id = $ary['id'];
    $name = $ary['name'];
    echo "<p>{$id} {$name}</p>";
}

function formatFriend($obj) {
    $id = $obj->id;
    $name = $obj->name;
    $picture = $obj->picture->data->url;

    echo "<p>{$id} {$name} <img src='{$picture}' /></p>";
}

function formatGroup($obj) {
    $id = $obj->id;
    $name = $obj->name;
    echo "<p>{$id} {$name}</p>";
}
?>