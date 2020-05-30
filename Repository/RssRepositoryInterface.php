<?php
namespace Repository;

interface RssRepositoryInterface
{
    public function checkItemExists($url);
    public function insertData(array $data);
    public function deleteDataByConditions(array $conditions);
}
