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

    echo "<form action='{$saveUrl}' method='post' name='saveForm'>";

    // Get personal info
    $request = new FacebookRequest($session, 'GET', '/me');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $me = $graphObject->asArray();
    formatMeInput($me);

    // Get personal friends
    $request = new FacebookRequest($session, 'GET', '/me/taggable_friends');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $friends = $graphObject->asArray();
    if (isset($friends['data'])) {
        formatFriendInput($friends['data']);
    }

    // Get personal groups
    $request = new FacebookRequest($session, 'GET', '/me/groups');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $groups = $graphObject->asArray();
    if (isset($groups['data'])) {
        formatGroupInput($groups['data']);
    }

    // Get personal feeds
    // $request = new FacebookRequest($session, 'GET', '/me/feed');
    // $response = $request->execute();
    // $graphObject = $response->getGraphObject();
    // var_dump($graphObject);
    
    echo '</form>';
    echo '<script language="javascript">document.saveForm.submit();</script>';
}

function formatMeInput($ary) {
    $id = $ary['id'];
    $name = $ary['name'];
    $jsonAry = array('id' => $id, 'name' => $name);

    $json = json_encode($jsonAry);
    echo "<input type='hidden' name='formatMe' value='{$json}' />";
}

function formatFriendInput($ary) {
    $jsonAry = array();
    foreach ($ary as $friend) {
        $id = $friend->id;
        $name = $friend->name;
        $picture = $friend->picture->data->url;
        $jsonAry[] = array('id' => $id, 'name' => $name, 'picture' => $picture);
    }

    $json = json_encode($jsonAry);
    echo "<input type='hidden' name='formatFriend' value='{$json}' />";
}

function formatGroupInput($ary) {
    $jsonAry = array();
    foreach ($ary as $group) {
        $id = $group->id;
        $name = $group->name;
        $jsonAry[] = array('id' => $id, 'name' => $name);
    }

    $json = json_encode($jsonAry);
    echo "<input type='hidden' name='formatGroup' value='{$json}' />";
}
?>