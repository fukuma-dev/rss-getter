<?php
use PHPUnit\Framework\TestCase;
include 'Controller/RssController.php';

use Controller\RssController;
use Repository\RssRepository;
use Service\RssService;

class RssControllerTest extends TestCase
{
    public function testSearchParamsError()
    {
        $controller = new RssController();

        $params['server_number'] = 'abc';
        $params['entry_number'] = 'def';
        $results = $controller->search($params);

        $this->assertSame(["サーバー番号は整数を入力してください。", "エントリーNo.は整数を入力してください。"], $results['error']);
    }
}
