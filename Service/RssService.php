<?php
namespace Service;
include __DIR__ . '/../Repository/RssRepository.php';

use Repository\RssRepository;

class RssService
{
    private $repository;

    /**
     * 1ページあたりの表示記事(ページャ用)
     */
    const DISPLAY_MAX = 5;
    /**
     * 完全一致を条件とするパラメータ
     */
    const EQUAL_TO_CONDITION_PARAMS = ['server_number', 'entry_number'];
    /**
     * 部分一致を条件とするパラメータ
     */
    const LIKE_CONDITION_PARAMS = ['post_datetime', 'url', 'user_name'];

    public function __construct($db)
    {
        $this->repository = new RssRepository($db);
    }

    /**
     * 検索結果用にデータを取得
     *
     * @param array $params
     * @return array
     */
    public function getDataByCondition(array $params)
    {
        $conditions = $this->createCondition($params);

        // ページャ用にレコード件数取得
        $recordCounts = $this->repository->getCountDisplayData($conditions);

        // 検索結果用の条件作成
        $pageId = $_GET['page_id'] ?: 1;
        $offset = ($pageId - 1) * self::DISPLAY_MAX;

        $results = $this->repository->getDisplayDataForResultPage($conditions, self::DISPLAY_MAX, $offset);

        return ['results' => $results, 'record_counts' => $recordCounts, 'display_max' => self::DISPLAY_MAX];
    }

    /**
     * 検索条件の作成
     *
     * @param $params
     * @return array|string
     */
    private function createCondition($params)
    {
        $conditions = [];
        foreach ($params as $key => $value) {
            if ($key == 'page_id') {
                continue;
            }
            // page_id以外の検索条件をクッキーに保存
            $this->setCookie($key, $value);

            // = もしくは >= の検索条件を作成
            if ($value !== '' && in_array($key, self::EQUAL_TO_CONDITION_PARAMS)) {
                if ($key == 'entry_number' && $value !== '' && $params['is_greater_than_or_equal_to'] === 'checked') {
                    array_push($conditions, $this->repository->getGreaterThanOrEqualToCondition($key, $value));
                    continue;
                }
                array_push($conditions, $this->repository->getEqualToCondition($key, $value));
                continue;
            }

            // 部分一致の条件を作成
            if ($value !== '' && in_array($key, self::LIKE_CONDITION_PARAMS)) {
                array_push($conditions, $this->repository->getLikeCondition($key, $value));
            }
        }
        if ($conditions !== []) {
            $conditions = $this->repository->combineSearchConditions($conditions);
        }

        return $conditions;
    }

    /**
     * クッキーを保存する
     *
     * @param $key
     * @param $value
     */
    private function setCookie($key, $value)
    {
        setcookie($key, $value ?: '', time()+60*60*24*7);
    }
}
