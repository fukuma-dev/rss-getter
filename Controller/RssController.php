<?php
namespace Controller;
include 'plugins/db.php';
include 'Repository/RssRepository.php';
include 'Service/RssService.php';
require_once __DIR__.'/../vendor/autoload.php';

use plugins\db;
use Dotenv\Dotenv;
use Repository\RssRepository;
use Service\RssService;

class RssController
{
    /**
     * RSS検索
     *
     * @param array $params
     * @return array
     */
    public function search(array $params)
    {
        if ($params !== []) {
            $error = $this->validate($params);
            if ($error !== []) {
                return ['error' => $error];
            }
        }

        $env = Dotenv::createImmutable(__DIR__.'/..');
        $env->load();

        $db = new db();
        $db = $db->dbConnect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

        $repository = new RssRepository($db);
        $rssService = new RssService($repository);

        $data = $rssService->getDataByParams($params);
        $db->close();

        return $data;
    }

    /**
     * フォームのバリデート
     *
     * @param $params
     * @return array
     */
    private function validate($params)
    {
        $error = [];

        // サーバー番号
        if($params['server_number'] !== '' && !ctype_digit($params['server_number'])) {
            array_push($error, "サーバー番号は整数を入力してください。");
        }

        // エントリーNo
        if($params['entry_number'] !== '' && !ctype_digit($params['entry_number'])) {
            array_push($error, "エントリーNo.は整数を入力してください。");
        }

        return $error;
    }

}
