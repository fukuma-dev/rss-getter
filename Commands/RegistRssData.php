<?php
include 'plugins/db.php';
include __DIR__.'/../Repository/RssRepository.php';
include __DIR__.'/../Service/RssParser/RssParserService.php';
include __DIR__.'/../Service/UrlParser/Fc2UrlParser.php';
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use Service\RssParser\RssParserService;
use Service\UrlParser\Fc2UrlParser;
use Repository\RssRepository;
use plugins\db;

const TARGET_URL = 'https://blog.fc2.com/newentry.rdf';

$rssParser = new RssParserService();

$parsedRssData = $rssParser->getRssDataByParsing(TARGET_URL);

$dataForDb = [];
$fc2UrlParser = new Fc2UrlParser();

foreach ($parsedRssData as $data) {
    $dataByParsedUrl = $fc2UrlParser->getDataByParsingUrl($data['url']);
    array_push($dataForDb, array_merge($data, $dataByParsedUrl));
}

$env = Dotenv::createImmutable(__DIR__.'/..');
$env->load();

$db = new db();
$db = $db->dbConnect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

$rssRepository = new RssRepository($db);

foreach ($dataForDb as $data) {
    $itemExists = $rssRepository->checkItemExists($data['url']);
    if ($itemExists == false) {
        $rssRepository->insertData($data);
    }
}

$db->close();
exit(0);
