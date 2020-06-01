<?php
namespace Repository;

interface RssRepositoryInterface
{
    public function getEqualToCondition($key, $value);

    public function getGreaterThanOrEqualToCondition($key, $value);

    public function getLikeCondition($key, $value);

    public function combineSearchConditions(array $conditions);

    public function getRecordCounts($searchConditions = []);

    public function getDisplayDataForResultPage($conditions, $limit, $offset);

    public function checkItemExists($url);

    public function insertData(array $data);

    public function deleteDataByConditions(array $conditions);
}
