<?php
namespace Service;

use Repository\RssRepositoryInterface;

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

    public function __construct(RssRepositoryInterface $repository)
    {
        $this->repository = $repository;
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

        // is_greater... のチェックボックスにチェックがないと値が送られないがクッキーは更新したい
        // (下記URLのようにhiddenでvalueを送る手段もあるが、同じクエリストリングが2つ付くため避けたい)
        // http://isket.jp/%E3%83%97%E3%83%AD%E3%82%B0%E3%83%A9%E3%83%9F%E3%83%B3%E3%82%B0/form%E3%81%A7checkbox%E3%81%AE%E3%83%91%E3%83%A9%E3%83%A1%E3%83%BC%E3%82%BF%E3%82%92%E5%BF%85%E3%81%9A%E9%80%81%E3%82%8B%E6%96%B9%E6%B3%95/
        if (! $params['is_greater_than_or_equal_to']) {
            $this->setCookie('is_greater_than_or_equal_to', '');
        }

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
