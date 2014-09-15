<?php

function get_dbname() {
    return $dbname = "risteily";
}

function get_dbuser() {
    return $dbuser = "root";
}

function get_dbpw() {
    return $dbpw = "password";
}

function get_dbhost() {
    return $dbhost = "localhost";
}

function get_dbsocket() {
    return $dbsocket = "";
}

function get_dbport(){
    $dbport = ini_get("mysqli.default_port");
    return $dbport;
}

function get_etuliite() {
    return $etuliite = "ristiely_";
}

?>