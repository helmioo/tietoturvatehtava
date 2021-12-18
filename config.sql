-- Tietokannan luontilauseet

-- Jos kanta on jo olemassa, poistetaan se ja luodaan uusi
drop database if exists n0oihe00;
create database n0oihe00;
use n0oihe00;

-- User-taulun luontilause
CREATE TABLE user (
    username VARCHAR(30) PRIMARY KEY,
    password VARCHAR(150) NOT NULL
    );

-- Lisätietoja-taulun luontilause
CREATE TABLE user_add_info (
    username VARCHAR(30),
    firstname VARCHAR(50), 
    lastname VARCHAR(50), 
    street_address VARCHAR(50),
    town VARCHAR(30),
    placeofwork VARCHAR(80),
    interests VARCHAR(200),
    FOREIGN KEY (username) REFERENCES user(username)
    );