<?php
use PHPUnit\Framework\TestCase;
include 'Service/UrlParser/Fc2UrlParser.php';

use Service\UrlParser\Fc2UrlParser;

class Fc2UrlParserTest extends TestCase
{
    public function testGetDateByParsingUrl()
    {
        $service = new Fc2UrlParser();

        $url = 'http://user5.blog50.fc2.com/blog-entry-1000.html';
        $results = $service->getDataByParsingUrl($url);

        $this->assertSame('user5', $results['user_name']);
        $this->assertSame('50', $results['server_number']);
        $this->assertSame('1000', $results['entry_number']);

        $url = 'http://fc2.blog.fc2.com/blog-entry-1000000000.html';
        $results = $service->getDataByParsingUrl($url);

        $this->assertSame('fc2', $results['user_name']);
        $this->assertSame('', $results['server_number']);
        $this->assertSame('1000000000', $results['entry_number']);
    }
}
