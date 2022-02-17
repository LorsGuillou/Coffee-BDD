<?php

require ('vendor/autoload.php');

if ($_SERVER['HTTP_HOST'] != 'https://coffee-k6-lg.herokuapp.com/') {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo 'Hello World !' . '<br>';

$path = 'mysql:host=' . $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';

$pdo = new PDO (
            $path,
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
$sqlQuery = 'SELECT name FROM waiter';
$execQuery = $pdo->query($sqlQuery);

$waiters = $execQuery->fetchAll();

foreach ($waiters as $waiter) {
    print $waiter['name'] . '<br>';
}
