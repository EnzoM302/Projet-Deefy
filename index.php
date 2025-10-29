<?php

use iutnc\deefy\Dispatch;
require __DIR__ . '/vendor/autoload.php';
session_start();


\iutnc\deefy\repository\DeefyRepository::setConfig('db.config.ini');


$action = isset($_GET['action']) ? $_GET['action'] : 'default';
$dispatcher = new Dispatch\Dispatcher($action);
$dispatcher->run();