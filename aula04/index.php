<?php

require_once '_app/Config.php';

$Login = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_birthday', 'user_friends', 'user_likes'];

try {
    if (isset($_SESSION['facebook_access_token'])) {
        $accessToken = $_SESSION['facebook_access_token'];
    } else {
        $accessToken = $Login->getAccessToken();
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
if (isset($accessToken)) {
    if (isset($_SESSION['facebook_access_token'])) {
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    } else {
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        $oAuth2Client = $fb->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    if (isset($_GET['error_code'])) {
        header('Location: ./');
    }
    try {
        $profile_request = $fb->get('/me?fields=name,first_name,last_name,email,birthday,taggable_friends.limit(2),likes');
        $profile = $profile_request->getGraphNode()->asArray();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    var_dump($profile);

//*******Lista de Amigos*******
//
//    echo 'Sua lista de Amigos!';
//    echo '<hr>';
//    foreach ($profile['taggable_friends'] as $friendslist):
//Recuperando a foto do perfil dos amigos
//        echo "<img src='".$friendslist['picture']['url']."'>";
//        echo ' - '.$friendslist['name'] . '</br>';
//    endforeach;
//    echo '<hr>';
//    
//*******Paginas Curtidas*******
//
//    echo 'Suas lista de Curtidas';
//    echo '<hr>';
//    foreach ($profile['likes'] as $likes):
//        echo ' - '.$likes['name'] . '</br>';
//    endforeach;
//    echo '<hr>';


    $logoff = filter_input(INPUT_GET, 'sair', FILTER_DEFAULT);
    if (isset($logoff) && $logoff == 'true'):
        session_destroy();
        header("Location: ./");
    endif;

    echo '<a href="?sair=true">Sair</a>';
    var_dump($_SESSION);
}else {
    $loginUrl = $Login->getLoginUrl('http://localhost/fb/aula04/index.php', $permissions);
    echo '<a href="' . $loginUrl . '">Entrar com facebook</a>';
    echo $accessToken;
    var_dump($_SESSION);
}