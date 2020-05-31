<?php
namespace Service\RssParser;
include 'BaseRssParser.php';

class Rss10Parser extends BaseRssParser {

    // RSS1.0(RDF)にフォーマットを合わせる
    Const DC_NAMESPACE = 'http://purl.org/dc/elements/1.1/';

    public function getTitle($item) {
        return $item->title;
    }

    public function getLink($item) {
        return $item->link;
    }

    public function getDescription($item) {
        if (isset($item->description)) {
            return $item->description;
        } else {
            return null;
        }
    }

    public function getDate($item) {
        if (isset($item->children(self::DC_NAMESPACE)->date)) {
            return $item->children(self::DC_NAMESPACE)->date;
        } else {
            return null;
        }
    }
}
