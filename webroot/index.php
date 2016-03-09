<?php
define('WEBROOT', __DIR__);
umask(0000);
ini_set('display_errors', 1);

require '../vendor/autoload.php';
require '../app/bootstrap.php';
require '../app/bootstrap_web.php';

$app = new \Eng\Core\Application('Web', $c);
$response = $app->handle($c['request'], \Eng\Core\Application::MASTER_REQUEST, !$c['config']['debug']);
$response->send();
