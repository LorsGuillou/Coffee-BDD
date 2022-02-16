<?php

echo 'Hello World !';

$bdd = new PDO("mysql:dbname=abclight;host=localhost", "root", "");

$req = $bdd->query("SELECT name FROM waiter");

$waiters = $req->fetchAll();

var_dump($waiters);

