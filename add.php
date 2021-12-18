<?php

// Firebase käyttöön
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once './inc/functions.php';
require_once './inc/headers.php';

// Haetaan otsikot pyynnöstä
$requestHeaders =  apache_request_headers();

// Auth Headerin varmistus
if (isset($requestHeaders['authorization'])) {

    // Halkaistaan osiin Bearer ja token
    $auth_value = explode(' ', $requestHeaders['authorization']);

    // Tarkistetaan Bearer
    if ($auth_value[0] === 'Bearer') {

        // Otetaan itse token talteen
        $token = $auth_value[1];

        try {
            // Tarkistetaan ja dekoodataan token, jos ei validi, siirtyy catchiin
            $decoded = JWT::decode($token, new Key(base64_encode('mysecret'), 'HS256'));

            // Onnistunut dekoodaus sisältää sub-kentän jossa käyttäjänimi
            $username = $decoded->sub;
            // Kutsutaan funktiota tietojen lisäys
            addInfo(openDb(), $username);

            // Catch mahdollisten virhetilanteiden varalta
        } catch (Exception) {
            echo  json_encode(array("message" => "No access!"));
        }
    }
}
