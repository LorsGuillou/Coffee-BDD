<?php

require ('vendor/autoload.php');
use Carbon\Carbon;

if ($_SERVER['HTTP_HOST'] != 'coffee-k6-lg.herokuapp.com') {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}



// Connexion à la base de données au travers du fichier .env

$path = 'mysql:host=' . $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';

$pdo = new PDO (
            $path,
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

echo '<h3>Afficher le nom des serveurs</h3><br>'; 

$sqlQuery = 'SELECT name FROM waiter';
$execQuery = $pdo->query($sqlQuery);
$waiters = $execQuery->fetchAll();

foreach ($waiters as $waiter) {
    print $waiter['name'] . '<br>';
}

// echo '----------------------' . '<br>';

// // Afficher les commandes (la colonne 'price' n'existe plus dans 'edible')

// $edibleQuery = 'SELECT * FROM edible';
// $execEdible = $pdo->query($edibleQuery);
// $edibles = $execEdible->fetchAll();

// foreach ($edibles as $edible) {
//     print $edible['name'] . ' - ' . $edible['price'] . ' €<br>';
// }

// echo '----------------------' . '<br>';

// // Afficher les cafés qui coûtent 1.3 € (la colonne 'price' n'existe plus dans 'edible')

// $samePrice = 'SELECT name, price FROM edible WHERE price LIKE 1.3';
// $priceQuery = $pdo->query($samePrice);
// $coffees = $priceQuery->fetchAll();

// foreach ($coffees as $coffee) {
//     print $coffee['name'] . ' - ' . $coffee['price'] . ' €<br>';
// }

echo '----------------------' . '<br>';

echo '<h3>Total facture 1</h3><br>';

$invoiceOne = 'SELECT price, quantity 
                FROM orderedible 
                WHERE idOrder LIKE 1';

$oneQuery = $pdo->query($invoiceOne);
$orders = $oneQuery->fetchAll();

$total = 0;
foreach ($orders as $order) {
    $total += ($order['price'] * $order['quantity']);
}

print $total . ' €<br>';

echo '----------------------' . '<br>';

echo "<h3>Chiffre d'affaire serveur 2</h3><br>";

/* Soluce Nico = SELECT * FROM `order` AS o INNER JOIN `orderedible` AS oe ON o.id = oe.idOrder WHERE o.idWaiter = 2; */
/* SELECT FORMAT (SUM(`price` * `quantity`), 2) AS `turnover` FROM `order` AS o INNER JOIN `orderedible` AS oe ON o.id = oe.idOrder WHERE o.idWaiter = 2; */

$totalIncomeWaiter2 = 'SELECT `w`.`name` AS `name`, FORMAT (SUM(`price` * `quantity`), 2) AS `turnover` 
                        FROM `order` AS `o` 
                        INNER JOIN `orderedible` AS `oe` 
                        INNER JOIN `waiter` AS `w` 
                        ON `o`.`id` = `oe`.`idOrder` 
                        WHERE `o`.`idWaiter` = 2
                        AND `w`.`id` = `o`.`idWaiter`';

$totalWaiter2 = $pdo->query($totalIncomeWaiter2)->fetch(PDO::FETCH_OBJ);

print $totalWaiter2->name . " a un chiffre d'affaire de " . $totalWaiter2->turnover . ' €<br>';

echo '----------------------' . '<br>';

echo '<h3>Savoir le nom des serveurs ayant servi la table 3</h3><br>';

/* SELECT `name` FROM `waiter`, `order` WHERE `idRestaurantTable` = 3 AND `idWaiter` = `waiter`.`id`;  */

$waitersTable3 = 'SELECT `name`
                    FROM `waiter`, `order` 
                    WHERE `idRestaurantTable` = 3 
                    AND `idWaiter` = `waiter`.`id`';

$waitersNames = $pdo->query($waitersTable3)->fetchAll();

foreach ($waitersNames as $waiterName) {
    print $waiterName['name'] . '<br>';
}

echo '----------------------' . '<br>';

echo '<h3>Connaître le ou les café(s) le(s) plus consommé(s)</h3><br>';

$mostSold = 'SELECT e.name, SUM(oe.quantity) AS total 
            FROM `OrderEdible` AS oe 
            INNER JOIN `Edible` AS e 
            ON e.id = oe.idEdible
            GROUP BY oe.idEdible  
            HAVING total = (
                SELECT SUM(oe.quantity) AS total 
                FROM `OrderEdible` AS oe 
                INNER JOIN `Edible` AS e 
                ON e.id = oe.idEdible 
                GROUP BY oe.idEdible 
                ORDER BY total DESC LIMIT 1
            );';

$bestCoffee = $pdo->query($mostSold)->fetch(PDO::FETCH_OBJ);

print 'Le café le plus vendu est : ' . $bestCoffee->name . ' avec un total de ' . $bestCoffee->total . ' consommations.<br>';

echo '----------------------' . '<br>';

echo '<h3>Afficher les informations de commande du serveur 2</h3><br>';

$dataWaiter = 'SELECT w.name, o.createdAt, FORMAT (SUM(oe.`price` * oe.`quantity`), 2) AS `turnover` 
                FROM `waiter` AS w 
                INNER JOIN `order` AS o 
                INNER JOIN `orderedible` AS oe 
                ON o.id = oe.idOrder 
                WHERE w.id = 2 
                AND o.idWaiter = w.id 
                GROUP BY oe.idOrder;';
            
$infoWaiter = $pdo->query($dataWaiter)->fetchAll();

foreach ($infoWaiter as $info) {
    $carbon = Carbon::parse($info['createdAt']);
    print 'Date : ' . $carbon->locale('fr')->diffForHumans() . ' - Le serveur ' . $info['name'] . ' a fait un profit de ' . $info['turnover'] . ' €<br>';
}

// SELECT w.name AS waiter, o.createdAt AS creationDate, FORMAT(SUM(oe.price * oe.quantity), 2) AS turnover 
// FROM `Order` AS o
// INNER JOIN `Waiter` AS w ON o.idWaiter = w.id
// INNER JOIN `OrderEdible` AS oe ON oe.idOrder = o.id
// WHERE w.id = 2 GROUP BY oe.idOrder;
 
 
// Alternative :
// SELECT name, createdAt, FORMAT(SUM(price), 2) AS facture 
// FROM `Waiter`,`Order`, `OrderEdible` 
// WHERE `Waiter`.id=`Order`.idWaiter 
// AND `Order`.id=`OrderEdible`.idOrder AND idWaiter=2 GROUP BY `Order`.id;