<?php
include __DIR__ . '/../Repository/RssRepository.php';
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use Repository\RssRepository;

$db = dbConnection();
$rssRepository = new RssRepository($db);

// ２週間経過したデータを削除
$conditions = [['create_date', '<', 'DATE_SUB(CURDATE(), INTERVAL 2 WEEK)']];
$rssRepository->deleteDataByConditions($conditions);

function dbConnection()
{
    $env = Dotenv::createImmutable(__DIR__.'/..');
    $env->load();

    // MySQL接続
    $db = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    } else {
        $db->set_charset("utf8mb4");
        return $db;
    }
}

