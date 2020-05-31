<?php
namespace Service\RssParser;

abstract class BaseRssParser {

    abstract protected function getTitle($item);
    abstract protected function getLink($item);
    abstract protected function getDescription($item);
    abstract protected function getDate($item);

    public function parse($item) {
        $items['title'] = (string)$this->getTitle($item) ?: '';
        $items['description'] = (string)$this->getDescription($item) ?: '';
        $items['url'] = (string)$this->getLink($item) ?: '' ;
        $items['post_datetime'] = date("Y-m-d H:i:s", strtotime((string)$this->getDate($item))) ?: '';

        return $items;
    }

}
