<?php


namespace AcikVeri\Importer\JSONImporter;

use Illuminate\Database\Eloquent\Model;

class JSONImporterRelation
{
    public $model;
    public $importer;
    public $json;
    public $index;

    public function __construct(Model $model = null, JSONImporter $importer = null,  $json = null, int $index = null)
    {
        $this->model = $model;
        $this->importer = $importer;
        $this->json = $json;
        $this->index = $index;
    }
}
