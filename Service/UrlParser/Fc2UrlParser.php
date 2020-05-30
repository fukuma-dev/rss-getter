<?php
namespace Service\UrlParser;

class Fc2UrlParser
{
    public function getDataByParsingUrl($url)
    {
        $parsedUrl = parse_url($url);
        $items = [];

        // ユーザー名
        $items['user_name'] = mb_strstr($parsedUrl['host'], '.', true) ?: '';

        // .fc2.comより前の文字列
        $stringBeforeFc2Com = mb_strstr($parsedUrl['host'], '.fc2.com', true);

        // サーバー番号の取得
        $items['server_number'] = str_replace('.blog', '', mb_strstr($stringBeforeFc2Com, '.blog')) ?: '';

        // .html前の文字列
        $stringBeforeHtml = mb_strstr($parsedUrl['path'], '.html', true);

        // エントリーNo.の取得
        $items['entry_number']= str_replace('/blog-entry-', '', mb_strstr($stringBeforeHtml, '/blog-entry-')) ?: '';

        return $items;
    }
}
