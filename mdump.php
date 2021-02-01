<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = 'mattwood_wp2';
$user = 'mattwood_wp2';
$pass = 'Z.ehCJtu7VNR9dLKwfv61';
$host = 'localhost:3306';
$dir = dirname(__FILE__) . '/dump.sql';

include_once(dirname(__FILE__) . '/mysqldump/src/Ifsnop/Mysqldump/Mysqldump.php');
$dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host=localhost;dbname='.$database, $user, $pass);
$dump->start('dump.sql');
?>