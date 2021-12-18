<?php

require_once './inc/functions.php';
require_once './inc/headers.php';

// Luetaan muuttujat mahdollisista inputeista ja sanitoidaan
$input = json_decode(file_get_contents('php://input'));
$firstname = filter_var($input->firstname, FILTER_SANITIZE_STRING);
$lastname = filter_var($input->lastname, FILTER_SANITIZE_STRING);
$street_address = filter_var($input->street_address, FILTER_SANITIZE_STRING);
$town = filter_var($input->town, FILTER_SANITIZE_STRING);
$placeofwork = filter_var($input->placeofwork, FILTER_SANITIZE_STRING);
$interests = filter_var($input->interests, FILTER_SANITIZE_STRING);

try {
    // Avataan tietokantayhteys
    $db = openDb();
    // SQL-komento, arvot parametreina
    $sql = "INSERT IGNORE INTO user_add_info (firstname, lastname, street_address, town, placeofwork, interests)
     VALUES (?,?,?,?,?,?)";
    // Valmistellaan komento ja bindataan arvot
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':firstname', $firstname, PDO::PARAM_STR);
    $prepare->bindValue(':lastname', $lastname, PDO::PARAM_STR);
    $prepare->bindValue(':street_address', $street_address, PDO::PARAM_STR);
    $prepare->bindValue(':town', $town, PDO::PARAM_STR);
    $prepare->bindValue(':placeofwork', $placeofwork, PDO::PARAM_STR);
    $prepare->bindValue(':interests', $interests, PDO::PARAM_STR);
    // Tiedot tietokantaan
    $prepare->execute(array('firstname' => $firstname, 
    'lastname' => $lastname, 'street_address' => $street_address, 
    'town' => $town, 'placeofwork' => $placeofwork, 'interests' => $interests));
    header('HTTP/1.1 200 OK');
    // Tietojen tulostaminen sivulle
    /* $data = array('firstname' => $firstname, 
        'lastname' => $lastname, 'street_address' => $street_address, 
        'town' => $town, 'placeofwork' => $placeofwork, 'interests' => $interests);
    print json_encode($data); */

    // Catch mahdollisten virhetilanteiden varalta
} catch (PDOException $pdoex) {
    returnError($pdoex);
}
