<?php
namespace Repository\Traits;

trait MysqlTrait
{
    /**
     * 検索条件をMySQL用のWhereに整形する
     *
     * @param array $conditions
     * @return string
     */
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

