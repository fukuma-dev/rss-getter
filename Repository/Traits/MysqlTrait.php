<?php
namespace Repository\Traits;

trait MysqlTrait
{
    public function createWhereByConditions(array $conditions)
    {
        $where = [];

        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                array_push($where, implode(' ', $condition));
            }
            $where = implode(' AND ', $where);
        }

        return $where;
    }
}

