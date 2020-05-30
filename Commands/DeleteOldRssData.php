<?php
include __DIR__.'/../Repository/RssRepository.php';
use Repository\RssRepository;

$rssRepository = new RssRepository('rss_reader', '127.0.0.1', 'root', '');

// ２週間経過したデータを削除
$conditions = [['create_date', '<', 'DATE_SUB(CURDATE(), INTERVAL 2 WEEK)']];
$rssRepository->deleteDataByConditions($conditions);
