<?php

function bdd() {
    try {
        $bdd = new PDO("mysql:dbname=abclight;host=mysql-69239-0.cloudclusters.net:1052;charset=utf8", "admin", "zerzLA7A");
    } catch(PDOException $e) {
        echo "Connexion impossible : " . $e->getMessage();
    }
    return $bdd;
}

function waiters() {
    global $bdd;

    $req = $bdd->query("SELECT name FROM waiter");

    $waiters = $req->fetchAll();

    return $waiters;
}