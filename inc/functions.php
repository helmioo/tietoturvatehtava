<?php

// Tietokantayhteyden luonti
function openDb(): object
{
    $ini = parse_ini_file("./config.ini", true);
    $host = $ini['host'];
    $database = $ini['database'];
    $user = $ini['user'];
    $password = $ini['password'];
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// Mahdollisen virheen näyttö
function returnError(PDOException $pdoex): void
{
    header('HTTP/1.1 500 Internal Server Error');
    $error = array('error' => $pdoex->getMessage());
    echo json_encode($error);
    exit;
}

// Luodaan uusi käyttäjä
function createUser(PDO $db) {
    // Luetaan muuttujat mahdollisista inputeista ja sanitoidaan
    $input = json_decode(file_get_contents('php://input'));
    $username = filter_var($input->username, FILTER_SANITIZE_STRING);
    $password = filter_var($input->password, FILTER_SANITIZE_STRING);

    try {
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
        echo "Successful registration!";
        // Catch mahdollisten virhetilanteiden varalta
    } catch (PDOException $pdoex) {
        returnError($pdoex);
    }
}

// Tarkastetaan käyttäjän tiedot
function checkUser(PDO $db, $username, $password)
{   // Sanitoidaan
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    try {
        //SQL-komento, username parametrina
        $sql = "SELECT password FROM user WHERE username=?";
        // Valmistellaan komento ja bindataan arvot
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':username', $username, PDO::PARAM_STR);
        $prepare->bindValue(':password', $password, PDO::PARAM_STR);
        // Haetaan tiedot kannasta
        $prepare->execute(array($username));
        $rows = $prepare->fetchAll();

        // Käydään rivit läpi ja tarkistetaan salasana
        foreach ($rows as $row) {
            // Password-sarakken tieto
            $pw = $row["password"];
            // Tarkistetaan salasana tietokannan hashia vasten
            if (password_verify($password, $pw)) {
                return true;
            }
        }
        //Jos ei löytynyt vastaavuutta, palautetaan false
        return false;
        // Catch mahdollisten virhetilanteiden varalta
    } catch (PDOException $pdoex) {
        returnError($pdoex);
    }
}
// Tietojen lisäys
function addInfo(PDO $db, $username)
{
    // Luetaan muuttujat mahdollisista inputeista ja sanitoidaan
    $input = json_decode(file_get_contents('php://input'));
    $firstname = filter_var($input->firstname, FILTER_SANITIZE_STRING);
    $lastname = filter_var($input->lastname, FILTER_SANITIZE_STRING);
    $street_address = filter_var($input->street_address, FILTER_SANITIZE_STRING);
    $town = filter_var($input->town, FILTER_SANITIZE_STRING);
    $placeofwork = filter_var($input->placeofwork, FILTER_SANITIZE_STRING);
    $interests = filter_var($input->interests, FILTER_SANITIZE_STRING);

    try {
        // SQL-komento, arvot parametreina
        $sql = "INSERT IGNORE INTO user_add_info VALUES (?,?,?,?,?,?,?)";
        // Valmistellaan komento ja bindataan arvot
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':username', $username, PDO::PARAM_STR);
        $prepare->bindValue(':firstname', $firstname, PDO::PARAM_STR);
        $prepare->bindValue(':lastname', $lastname, PDO::PARAM_STR);
        $prepare->bindValue(':street_address', $street_address, PDO::PARAM_STR);
        $prepare->bindValue(':town', $town, PDO::PARAM_STR);
        $prepare->bindValue(':placeofwork', $placeofwork, PDO::PARAM_STR);
        $prepare->bindValue(':interests', $interests, PDO::PARAM_STR);
        // Tiedot tietokantaan
        $prepare->execute(array(
            $username, $firstname,
            $lastname, $street_address, $town, $placeofwork, $interests
        ));
        echo "Data successfully inserted!";
        // Catch mahdollisten virhetilanteiden varalta
    } catch (PDOException $pdoex) {
        returnError($pdoex);
    }
}

// Tietojen näyttäminen
function showPersonalData(PDO $db, $username)
{
    // Sanitoidaan
    $username = filter_var($username, FILTER_SANITIZE_STRING);

    try {
        // SQL-komento, arvo parametrina
        $sql = "SELECT * FROM user_add_info
        WHERE username=?";
        $prepare = $db->prepare($sql);
        $prepare->execute(array($username));
        $results = $prepare->fetchAll(PDO::FETCH_ASSOC);
        header('HTTP/1.1 200 OK');
        echo json_encode($results);
    } catch (PDOException $pdoex) {
        returnError($pdoex);
    }
}
