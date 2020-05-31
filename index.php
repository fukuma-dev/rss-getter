<?php
include "lib/BladeOne.php";
Use eftec\bladeone\BladeOne;

$views = __DIR__ . '/views'; // viewフォルダ
$cache = __DIR__ . '/cache'; // キャッシュフォルダ
$blade = new BladeOne($views, $cache,BladeOne::MODE_AUTO);

try {
    echo $blade->run("index");
    exit();
} catch (\Exception $e) {
    printf($e->getMessage());
    exit(1);
}

