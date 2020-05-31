<?php
namespace Controller;
include 'Service/RssService.php';
require_once __DIR__.'/../vendor/autoload.php';

use mysqli;
use Dotenv\Dotenv;
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
        $error = $this->validation($params);
        if ($error !== []) {
            return ['error' => $error];
        }

        $db = $this->dbConnection();
        $viewService = new RssService($db);

        return $viewService->getDataByCondition($params);
    }

    /**
     * フォームのバリデート
     *
     * @param $params
     * @return array
     */
    private function validation($params)
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

    /**
     * @return mysqli
     */
    private function dbConnection()
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
}
