<?php
namespace Service\RssParser;
include __DIR__.'/Rss10Parser.php';

class RssParserService
{
    private $Rss10Parser;

    public function __construct()
    {
        $this->Rss10Parser = new Rss10Parser;
    }

    /**
     * RSSを解析してデータを取得する
     *
     * @param $url
     * @return array
     */
    public function getRssDataByParsing($url)
    {
        $rssData = [];

        try {
            $content = file_get_contents($url);
            if ($content == false) {
                throw new \Exception("Failed to load file.\n");
            }

            // Status codeが200以外は処理しない
            $statusCode = $http_response_header[0];
            if (strpos($statusCode, '200') === false) {
                throw new \Exception("Unexpected status code: {$statusCode}\n");
            }

            $rss = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($rss == false) {
                throw new \Exception("Failed to simplexml_load_string\n");
            }

            if ($rss->getName() === 'RDF') {
                foreach ($rss->item as $item) {
                    array_push($rssData, $this->Rss10Parser->parse($item));
                }
            }
        } catch (\Exception $e) {
            printf($e->getMessage());
        }

        return $rssData;
    }
}
