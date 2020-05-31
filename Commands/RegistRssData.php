<?php
include __DIR__ . '/../Service/RssParser/RssParserService.php';
include __DIR__ . '/../Service/UrlParser/Fc2UrlParser.php';
include __DIR__ . '/../Repository/RssRepository.php';
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use Service\RssParser\RssParserService;
use Service\UrlParser\Fc2UrlParser;
use Repository\RssRepository;

const TARGET_URL = 'https://blog.fc2.com/newentry.rdf';

$rssParser = new RssParserService();

$parsedRssData = $rssParser->getRssDataByParsing(TARGET_URL);

$dataForDb = [];
$fc2UrlParser = new Fc2UrlParser();

foreach ($parsedRssData as $data) {
    $dataByParsedUrl = $fc2UrlParser->getDataByParsingUrl($data['url']);
    array_push($dataForDb, array_merge($data, $dataByParsedUrl));
}

$db = dbConnection();
$rssRepository = new RssRepository($db);

foreach ($dataForDb as $data) {
    $itemExists = $rssRepository->checkItemExists($data['url']);
    if ($itemExists == false) {
        $rssRepository->insertData($data);
    }
}

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
