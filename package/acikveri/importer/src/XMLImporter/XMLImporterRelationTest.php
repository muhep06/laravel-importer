<?php


namespace AcikVeri\Importer\XMLImporter;


use Illuminate\Database\Eloquent\Model;

class XMLImporterRelationTest
{
    public $model;
    public $importer;
    public $xml;
    public $index;

    public function __construct(Model $model = null, XMLImporterTest $importer = null, \SimpleXMLElement $xml = null, int $index = null)
    {
        $this->model = $model;
        $this->importer = $importer;
        $this->xml = $xml;
        $this->index = $index;
    }
}
