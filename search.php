<?php
include "lib/BladeOne.php";
include 'Controller/RssController.php';

Use eftec\bladeone\BladeOne;
use Controller\RssController;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views, $cache,BladeOne::MODE_AUTO);

$controller = new RssController();
$results = $controller->search();

echo $blade->run("search", ["data" => $results]);
