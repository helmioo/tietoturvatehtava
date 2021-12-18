<?php
// Sisällytetään Firebase-kirjasto
include('fb/BeforeValidException.php');
include('fb/ExpiredException.php');
include('fb/JWK.php');
include('fb/JWT.php');
include('fb/Key.php');
include('fb/SignatureInvalidException.php');

// Sallitaan liikenne paikalliselta serveriltä
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');
    header('Access-Control-Max-Age: 3600');
}

// Access-Control-Headerit käyttöön kun niitä tarvitaan
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit;
}