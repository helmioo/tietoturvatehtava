<?php

require_once './inc/functions.php';
require_once './inc/headers.php';

// Luetaan muuttujat mahdollisista inputeista ja sanitoidaan
$input = json_decode(file_get_contents('php://input'));
$username = filter_var($input->username, FILTER_SANITIZE_STRING);
$password = filter_var($input->password, FILTER_SANITIZE_STRING);

try {
    // Avataan tietokantayhteys
    $db = openDb();

    // Hashataan salasana
    $hash_pw = password_hash($password, PASSWORD_DEFAULT);
    // SQL-komento, arvot parametreina
    $sql = "INSERT IGNORE INTO user VALUES (?,?)";
    // Valmistellaan komento ja bindataan arvot
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':username', $username, PDO::PARAM_STR);
    $prepare->bindValue(':password', $password, PDO::PARAM_STR);
    // Tiedot tietokantaan
    $prepare->execute(array($username, $hash_pw));
    header('HTTP/1.1 200 OK');

    // Catch mahdollisten virhetilanteiden varalta
} catch (PDOException $pdoex) {
    returnError($pdoex);
}
