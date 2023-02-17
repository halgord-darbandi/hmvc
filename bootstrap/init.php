<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Core\Request\Request;

require $_ENV['BASE_PATH'] . 'helpers/helpers.php';
require $_ENV['BASE_PATH'] . 'route/web.php';


$request = new Request();