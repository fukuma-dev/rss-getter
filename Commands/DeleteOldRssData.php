<?php
include __DIR__.'/../plugins/db.php';
include __DIR__.'/../Repository/RssRepository.php';
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use plugins\db;
use Repository\RssRepository;

$env = Dotenv::createImmutable(__DIR__.'/..');
$env->load();

$db = new db();
$db = $db->dbConnect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

$rssRepository = new RssRepository($db);

// ２週間経過したデータを削除
$conditions = [['create_date', '<', 'DATE_SUB(CURDATE(), INTERVAL 2 WEEK)']];
$rssRepository->deleteDataByConditions($conditions);

$db->close();
exit(0);
