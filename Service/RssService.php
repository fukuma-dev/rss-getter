<?php
namespace Service;
include __DIR__ . '/../Repository/RssRepository.php';

use Repository\RssRepository;

class RssService
{
    private $repository;

    const DISPLAY_MAX = 5;
    const ALLOWED_PARAMS = ['post_datetime', 'url', 'user_name', 'server_number', 'entry_number'];

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

        return ['results' => $results, 'record_counts' => $recordCounts, 'display_max' => self::DISPLAY_MAX, 'allowed_display' => self::ALLOWED_PARAMS];
    }

    private function createCondition($params)
    {
        $conditions = [];
        foreach ($params as $key => $value) {
            if ($key == 'page_id') {
                continue;
            }
            // page_id以外の検索条件をクッキーに保存
            $this->setCookie($key, $value);

            if ($key == 'entry_number' && $value !== '') {
                array_push($conditions, $this->repository->getGreaterThanOrEqualToCondition($key, $value));
                continue;
            }

            if ($value !== '' && in_array($key, self::ALLOWED_PARAMS)) {
                array_push($conditions, $this->repository->getLikeCondition($key, $value));
            }
        }
        if ($conditions !== []) {
            $conditions = $this->repository->combineSearchConditions($conditions);
        }

        return $conditions;
    }

    private function setCookie($key, $value)
    {
        setcookie($key, $value ?: '');
    }
}
