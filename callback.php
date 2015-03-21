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
    $me = $response->getGraphObject()->asArray();
    $meJson = json_encode(formatMe($me));
    echoInput('formatMe', $meJson);

    // Get personal friends
    $request = new FacebookRequest($session, 'GET', '/me/taggable_friends');
    $response = $request->execute();
    $friends = $response->getGraphObject()->asArray();
    if (isset($friends['data'])) {
        $friendsJson = json_encode(formatFriend($friends['data']));
        echoInput('formatFriend', $friendsJson);
    }

    // Get personal groups
    $request = new FacebookRequest($session, 'GET', '/me/groups');
    $response = $request->execute();
    $groups = $response->getGraphObject()->asArray();
    if (isset($groups['data'])) {
        $groupsJson = json_encode(formatGroup($groups['data']));
        echoInput('formatGroup', $groupsJson);
    }

    // Get personal feeds
    // $request = new FacebookRequest($session, 'GET', '/me/feed');
    // $response = $request->execute();
    // $graphObject = $response->getGraphObject();
    // var_dump($graphObject);

    // Get personal fan pages
    $request = new FacebookRequest($session, 'GET', '/me/likes');
    $fanPagesJsonAry = array();
    do {
        $response = $request->execute();
        $fanPages = $response->getGraphObject()->asArray();
        if (isset($fanPages['data'])) {
            $fanPagesJsonAry = array_merge($fanPagesJsonAry, formatFanPage($fanPages['data']));
        }
    } while ($request = $response->getRequestForNextPage());
    if (!empty($fanPagesJsonAry)) {
        $fanPagesJson = json_encode($fanPagesJsonAry);
        echoInput('formatFanPage', $fanPagesJson);
    }

    echo '</form>';
    echo '<script language="javascript">document.saveForm.submit();</script>';
}

function formatMe($ary) {
    $id = $ary['id'];
    $name = $ary['name'];
    $jsonAry = array('id' => $id, 'name' => $name);

    return $jsonAry;
}

function formatFriend($ary) {
    $jsonAry = array();
    foreach ($ary as $friend) {
        $id = $friend->id;
        $name = $friend->name;
        $picture = $friend->picture->data->url;
        $jsonAry[] = array('id' => $id, 'name' => $name, 'picture' => $picture);
    }

    return $jsonAry;
}

function formatGroup($ary) {
    $jsonAry = array();
    foreach ($ary as $group) {
        $id = $group->id;
        $name = $group->name;
        $jsonAry[] = array('id' => $id, 'name' => $name);
    }

    return $jsonAry;
}

function formatFanPage($ary) {
    $jsonAry = array();
    foreach ($ary as $fanpage) {
        $id = $fanpage->id;
        $name = $fanpage->name;
        $category = $fanpage->category;
        $jsonAry[] = array('id' => $id, 'name' => $name, 'category' => $category);
    }
    return $jsonAry;
}

function echoInput($name, $value) {
    echo "<input type='hidden' name='{$name}' value='{$value}' />";
}
?>