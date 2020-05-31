<?php
namespace Repository;
include 'RssRepositoryInterface.php';
include 'Traits/MysqlTrait.php';

use Repository\Traits\MysqlTrait;
use mysqli;

class RssRepository implements RssRepositoryInterface
{
    use MysqlTrait;
    private $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
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

        return $this->db->query($query)->num_rows;
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

        $results = $this->db->query($query)->fetch_all();
        $this->db->close();

        return $results;
    }


    /**
     * 重複するレコードの有無チェック
     *
     * @param $url
     * @return bool
     */
    public function checkItemExists($url)
    {
        if ($url == '') {
            return false;
        }
        $query = "SELECT * FROM rss_data WHERE url = '{$url}' LIMIT 1";
        $results = $this->db->query($query);
        $isExists = $results->num_rows === 1;
        $results->close();

        return $isExists;
    }

    /**
     * 永続層に保存
     *
     * @param $data
     */
    public function insertData(array $data)
    {
        $query = $this->db->prepare("INSERT INTO rss_data (title, description, post_datetime, url, user_name, server_number, entry_number, create_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW());");

        if ($query) {
            $query->bind_param(
                'sssssii',
                $data['title'],
                $data['description'],
                $data['post_datetime'],
                $data['url'],
                $data['user_name'],
                $data['server_number'],
                $data['entry_number']
            );
            $query->execute();
            $query->close();
        }
    }

    /**
     * 条件を指定して永続層のRSSデータ削除
     *
     * @param array $conditions
     * @return bool
     */
    public function deleteDataByConditions(array $conditions)
    {
        if ($conditions === []) {
            return false;
        }

        $where = $this->createWhereByConditions($conditions);
        $rawQuery = "DELETE FROM rss_data WHERE {$where}";

        $this->db->begin_transaction();
        $this->db->query($rawQuery);
        $this->db->commit();
        $this->db->close();

        return true;
    }
    
}
