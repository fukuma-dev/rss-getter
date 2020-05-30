<?php
namespace Repository;

interface ViewRepositoryInterface
{
    public function getGreaterThanOrEqualToCondition($key, $value);

    public function getLikeCondition($key, $value);

    public function combineSearchConditions(array $conditions);

    public function getCountDisplayData($conditions = []);

    public function getDisplayDataForResultPage($conditions, $limit, $offset);
}
