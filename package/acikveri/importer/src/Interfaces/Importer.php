<?php


namespace AcikVeri\Importer\Interfaces;
use Closure;


interface Importer
{
    public function loadFromUrl(string $url);
    public function loadFromString(string $data);
    public function setModel(string $table);
    public function insert(string $column, string $key);
    public function get(string $path = null);
    public function relation(string $column, Closure $callback);
    public function update();
    public function import(bool $fresh = false);

}
