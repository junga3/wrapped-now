<?php
session_start();
$config = include 'config.php';
$clientID = $config['clientID'];
$clientSecret = $config['clientSecret'];
$redirect = $config['redirectURI'];
$loginPage = $config['loginPage'];

$codeVerif = bin2hex(random_bytes(50));
$codeChall = strtr(rtrim(base64_encode(hash('sha256', $codeVerif, true)), '='), '+/', '-_');
$_SESSION['codeVerif'] = $codeVerif;

function getToken($code){   
    $accessToken = null;
    
    // check if token exists in session
    if($_SESSION['token'] && isset($_SESSION['token'])){
        $token = $_SESSION['token'];

        // make a simple request to check if token is still valid
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$_SESSION['token']));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // if the http status code is 401, the token has expired
        if($httpCode == 401){
            unset($_SESSION['token']);
        } else{
            return $_SESSION['token'];
        }
    }

    if(isset($code)){
        global $clientID, $redirect, $clientSecret, $authCode; 
        $authCode = $code;
        $verif = $_SESSION['codeVerif'];

        
        // set params 
        // for some reason client id, secret and redirect uri need to be "$var" instead of just $var
        // if those 3 arent formatted like "$var", then i get:
        //     {"error":"invalid_grant","error_description":"Invalid redirect URI"} 
        // i have no idea why it is like this 
        $postData = array(
            'client_id' => "$clientID",
            'client_secret' => "$clientSecret",
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => "$redirect",
            'code_verifier' => $verif,
        );

        // curl to trade auth code for access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        $response = curl_exec($ch);
        if(!$response){
            die('Error: ' . curl_error($ch) . ' - Code: ' . curl_errno($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);
        
        $accessToken = $responseData['access_token'];
        $_SESSION['token'] = $accessToken; //store token as session var. expires after 24 minutes
    }
    return $accessToken;
}

function getUserData($ext, $code){
    $token = getToken($code);

    // check if token exists, if not, redirect back to login
    if(!$token || !isset($token)){
        global $loginPage;
        return json_encode(array(
            'error' => 'redirect', 
            'location' => "$loginPage", 
            'token' => "$token", 
            'code' => "$code"
        ));
    }

    $endpoint = "https://api.spotify.com/v1/me/$ext";

    // retrieve user data
    // curl get request as seen on spotify api docs (the token there is a placeholder dont worry)
    // curl --request GET \
    //   --url https://api.spotify.com/v1/me \
    //   --header 'Authorization: Bearer 1POdFZRZbvb...qqillRxMr2z'
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token));

    $response = curl_exec($ch);
    if(!$response){
        die('Error: ' . curl_error($ch) . ' - Code: ' . curl_errno($ch) . ' - HTTP status: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }

    curl_close($ch);

    return $response;

}

// get data from ajax and make api call
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $apiCall = $_POST['apiCall'];
    $code = $_POST['code'];
    $result = getUserData($apiCall, $code);
    echo $result;
}

?>