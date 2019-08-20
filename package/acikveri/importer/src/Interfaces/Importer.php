<?php


namespace AcikVeri\Importer\Interfaces;
use Closure;


interface Importer
{
    public function loadFromUrl($url);
    public function loadFromString($data);
    public function setTable($table);
    public function insert($column, $key);
    public function get(string $path);
    public function relation($column, Closure $callback);
    public function update();
    public function import();

}
