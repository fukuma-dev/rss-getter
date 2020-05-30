<?php
include __DIR__ . '/../Service/RssParser/RssParserService.php';
include __DIR__ . '/../Service/UrlParser/Fc2UrlParser.php';
include __DIR__.'/../Repository/RssRepository.php';

use Service\RssParser\RssParserService;
use Service\UrlParser\Fc2UrlParser;
use Repository\RssRepository;

// TODO: 外部からURL読み込む形にする
$targetRss = 'https://blog.fc2.com/newentry.rdf';

$rssParser = new RssParserService();
$parsedRssData = $rssParser->getRssDataByParsing($targetRss);

$fc2UrlParser = new Fc2UrlParser();
$dataForDb = [];

foreach ($parsedRssData as $data) {
    $dataByParsedUrl = $fc2UrlParser->getDataByParsingUrl($data['url']);
    array_push($dataForDb, array_merge($data, $dataByParsedUrl));
}

$rssRepository = new RssRepository();

foreach ($dataForDb as $data) {
    $itemExists = $rssRepository->checkItemExists($data['url']);
    if ($itemExists == false) {
        $rssRepository->insertData($data);
    }
}
