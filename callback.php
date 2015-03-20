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
    // $graphArray = $graphObject->asArray();

    // Get personal friends
    $request = new FacebookRequest($session, 'GET', '/me/friendlists');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    var_dump($graphObject);
    echo '<br />';

    // Get personal groups
    $request = new FacebookRequest($session, 'GET', '/me/groups');
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    var_dump($graphObject);
}

?>