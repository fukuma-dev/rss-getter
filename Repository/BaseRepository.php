<?php
namespace Repository;
require __DIR__.'/../vendor/autoload.php';

use mysqli;

abstract class BaseRepository
{
    protected $mysqli;

    public function __construct($host, $userName, $pass, $dbName)
    {
        $this->mysqli = new mysqli($host, $userName, $pass, $dbName);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        } else {
            $this->mysqli->set_charset("utf8mb4");
        }
    }

}
