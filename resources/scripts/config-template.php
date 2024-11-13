<?php

// replace the placeholders and gitignore it

// add ~/resources/scripts/config.php to .gitignore
// run sudo -u www-data git rm --cached path/resources/scripts/config.php
// in the scripts folder, run touch config.php, then nano config.php and paste the following code
// and replace the placeholders with the actual values

$clientID = 'placeholder';
$redirectURI = 'placeholder';
$clientSecret = 'placeholder';
$processedURI = urlencode($redirectURI);
$loginPage = 'placeholder';

return[
    'clientID' => $clientID,
    'redirectURI' => $redirectURI,
    'clientSecret' => $clientSecret,
    'processedURI' => $processedURI,
    'loginPage' => $loginPage,
];

?>