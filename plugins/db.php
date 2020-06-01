<?php
namespace plugins;
use mysqli;

class db
{
    /**
     * @param $host
     * @param $userName
     * @param $pass
     * @param $dbName
     * @return mysqli
     */
    public function dbConnect($host, $userName, $pass, $dbName)
    {
        // MySQL接続
        $db = new mysqli($host, $userName, $pass, $dbName);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        } else {
            $db->set_charset("utf8mb4");
            return $db;
        }
    }
}
