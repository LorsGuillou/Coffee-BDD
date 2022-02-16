<?php

function bdd() {
    try {
        $bdd = new PDO("mysql:dbname=abclight;host=localhost", "root", "");
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