<?php
namespace Repository;
include 'RssRepositoryInterface.php';
include 'Traits/MysqlTrait.php';
require __DIR__.'/../vendor/autoload.php';

use Repository\Traits\MysqlTrait;
use Dotenv\Dotenv;

class RssRepository extends BaseRepository implements RssRepositoryInterface
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
        $results = $this->mysqli->query($query);
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
        $query = $this->mysqli->prepare("INSERT INTO rss_data (title, description, post_datetime, url, user_name, server_number, entry_number, create_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW());");

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

        $this->mysqli->begin_transaction();
        $this->mysqli->query($rawQuery);
        $this->mysqli->commit();
        $this->mysqli->close();

        return true;
    }
}
