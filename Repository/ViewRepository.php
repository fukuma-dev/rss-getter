<?php
namespace Repository;
include 'BaseRepository.php';
include 'ViewRepositoryInterface.php';
include 'Traits/MysqlTrait.php';
require __DIR__.'/../vendor/autoload.php';

use Repository\Traits\MysqlTrait;
use Dotenv\Dotenv;

class ViewRepository extends BaseRepository implements ViewRepositoryInterface
{
    // TODO: Mysqlに依存させない
    use MysqlTrait;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/..');
        $dotenv->load();
        parent::__construct(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function getGreaterThanOrEqualToCondition($key, $value)
    {
        return [$key, '>=', $value];
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function getLikeCondition($key, $value)
    {
        return [$key, 'LIKE', "'%$value%'"];
    }

    /**
     * 検索条件をMySQL用のWhereに整形する
     *
     * @param array $conditions
     * @return string
     */
    public function combineSearchConditions(array $conditions)
    {
        return $this->createWhereByConditions($conditions);
    }

    /**
     * レコード数を取得
     *
     * @param array $conditions
     * @return int
     */
    public function getCountDisplayData($conditions = [])
    {
        if ($conditions !== []) {
            $query = "SELECT * FROM rss_data WHERE {$conditions}";
        } else {
            $query = "SELECT * FROM rss_data";
        }

        return $this->mysqli->query($query)->num_rows;
    }

    /**
     * 結果ページ用のクエリ実行
     *
     * @param $conditions
     * @param $limit
     * @param $offset
     * @return mixed
     */
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
