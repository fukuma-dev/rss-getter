<?php
namespace Repository;
include 'ViewRepositoryInterface.php';
include 'Traits/MysqlTrait.php';

use mysqli;
use Repository\Traits\MysqlTrait;

class ViewRepository implements ViewRepositoryInterface
{
    use MysqlTrait;
    private $mysqli;

    public function __construct()
    {
        // TODO: envから渡す
        $this->mysqli = new mysqli('127.0.0.1','root', '', 'rss_reader');
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        } else {
            $this->mysqli->set_charset("utf8mb4");
        }
    }

    public function getGreaterThanOrEqualToCondition($key, $value)
    {
        return [$key, '>=', $value];
    }

    public function getLikeCondition($key, $value)
    {
        return [$key, 'LIKE', "'%$value%'"];
    }

    public function combineSearchConditions(array $conditions)
    {
        return $this->createWhereByConditions($conditions);
    }

    public function getCountDisplayData($conditions = [])
    {
        if ($conditions !== []) {
            $query = "SELECT * FROM rss_data WHERE {$conditions}";
        } else {
            $query = "SELECT * FROM rss_data";
        }

        return $this->mysqli->query($query)->num_rows;
    }

    // TODO: 汎用的にする
    public function getDisplayDataForResultPage($conditions, $limit, $offset)
    {
        if ($conditions !== []) {
            $query = "SELECT post_datetime, url, title, description FROM rss_data WHERE {$conditions}  ORDER BY post_datetime desc LIMIT {$limit} OFFSET {$offset}";
        } else {
            $query = "SELECT post_datetime, url, title, description FROM rss_data ORDER BY post_datetime desc LIMIT {$limit} OFFSET {$offset}";
        }

        $results = $this->mysqli->query($query)->fetch_all();
        $this->mysqli->close();

        return $results;
    }

}
