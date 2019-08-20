<?php


namespace AcikVeri\Importer\Interfaces;


interface Importer
{
    public function loadFromUrl($url);
    public function loadFromString($data);
    public function setIndex(string $key);
    public function setTable($table);
    public function insert($column, $key);
    public function getIncludes();
    public function getIndex();
    public function update();
    public function import();

}
