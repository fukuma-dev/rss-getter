<?php
namespace Service;
include __DIR__.'/../Repository/ViewRepository.php';

use Repository\ViewRepository;

class ViewService
{
    private $repository;

    const DISPLAY_MAX = 5;
    const ALLOWED_PARAMS = ['post_datetime', 'url', 'user_name', 'server_number', 'entry_number'];

    public function __construct()
    {
        $this->repository = new ViewRepository();
    }

    public function getDataByCondition(array $params)
    {
        $results = [];
        $conditions = [];
        foreach ($params as $key => $value) {
            if ($key == 'page_id') {
                continue;
            }
            // page_id以外の検索条件をクッキーに保存
            $this->setCookie($key, $value);

            if ($key == 'entry_number' && $value !== '') {
                array_push($conditions, $this->repository->getGreaterThanOrEqualToCondition($key, $value));
            }

            if ($value !== '' && in_array($key, self::ALLOWED_PARAMS)) {
                array_push($conditions, $this->repository->getLikeCondition($key, $value));
            }
        }

        if ($conditions !== []) {
            $conditions = $this->repository->combineSearchConditions($conditions);
        }

        // ページャ用にレコード件数取得
        $recordCounts = $this->repository->getCountDisplayData($conditions);

        // 検索結果表示用のクエリ
        if ($recordCounts > 0) {
            $pageId = $_GET['page_id'] ?: 1;
            $limit = self::DISPLAY_MAX;
            $offset = ($pageId - 1) * self::DISPLAY_MAX;

            $results = $this->repository->getDisplayDataForResultPage($conditions, $limit, $offset);
        }

        return ['results' => $results, 'record_counts' => $recordCounts, 'display_max' => self::DISPLAY_MAX, 'allowed_display' => self::ALLOWED_PARAMS];
    }

    private function setCookie($key, $value)
    {
        setcookie($key, $value ?: '');
    }
}
