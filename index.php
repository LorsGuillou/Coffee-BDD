<?php
    require_once 'bdd.php';
    $bdd = bdd();

echo 'Hello World !' . '<br>';

$waiters = waiters();

foreach($waiters as $waiter) {
    echo $waiter['name'] . '<br>';
}
