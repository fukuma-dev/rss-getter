<?php
namespace Controller;
include 'Service/RssService.php';
require_once __DIR__.'/../vendor/autoload.php';

use mysqli;
use Dotenv\Dotenv;
use Service\RssService;

class RssController
{
    public function search()
    {
        $error = $this->validation($_GET);
        if ($error !== []) {
            return ['error' => $error];
        }

        $db = $this->dbConnection();
        $viewService = new RssService($db);

        return $viewService->getDataByCondition($_GET);
    }

    private function validation($params)
    {
        $error = [];

        // エントリーNo.のバリデーション
        if($params['entry_number'] !== '' && !ctype_digit($params['entry_number'])) {
            array_push($error, "エントリーNo.は整数を入力してください。");
        }

        return $error;
    }

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
