<?php
include "lib/BladeOne.php";
Use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views'; // viewフォルダ
$cache = __DIR__ . '/cache'; // キャッシュフォルダ
$blade = new BladeOne($views, $cache,BladeOne::MODE_AUTO);

echo $blade->run("index");
