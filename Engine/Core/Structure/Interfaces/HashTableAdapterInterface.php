<?php
namespace Core\Structure\Interfaces;

/**
 * Interface HashTableAdapter
 * @package Core\Structure\Interfaces
 */
interface HashTableAdapterInterface
{
    public function loadSource($source, $prefix);

    public function addData($key, $data);

    public function getData($key);

    public function checkData($key);

    public function clearData($key);

    public function sizeData();

    public function getDataByIndex($index);
}