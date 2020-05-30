<?php
include "lib/BladeOne.php";
include 'Service/ViewService.php';

Use eftec\bladeone\BladeOne;
use Service\ViewService;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views, $cache,BladeOne::MODE_AUTO);

$viewService = new ViewService();
$data = $viewService->getDataByCondition($_GET);

echo $blade->run("search", ["data" => $data]);
