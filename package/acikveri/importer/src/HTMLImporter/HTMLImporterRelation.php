<?php


namespace AcikVeri\Importer\HTMLImporter;


use Illuminate\Database\Eloquent\Model;

class HTMLImporterRelation
{
    public $model;
    public $importer;
    public $json;
    public $index;

    public function __construct(Model $model = null, HTMLImporter $importer = null,  $json = null, int $index = null)
    {
        $this->model = $model;
        $this->importer = $importer;
        $this->json = $json;
        $this->index = $index;
    }
}
