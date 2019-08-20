<?php


namespace AcikVeri\Importer\JSONImporter;

use AcikVeri\Importers\Models\DynamicModel;

class JSONImporterRelation
{
    private $model;
    private $parser;
    private $json;
    private $loopIndex;

    public function setModel(DynamicModel $model): void
    {
        $this->model = $model;
    }


    public function setParser(JSONImporter $parser): void
    {
        $this->parser = $parser;
    }


    public function setJson($json): void
    {
        $this->json = $json;
    }

    /**
     * @return mixed
     */
    public function getModel(): DynamicModel
    {
        return $this->model;
    }

    /**
     * @return JSONImporter
     */
    public function getParser()
    {
        return $this->parser;
    }

    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param integer $loopIndex
     * @return void
     */
    public function setLoopIndex($loopIndex)
    {
        $this->loopIndex = $loopIndex;
    }

    /**
     * @return int
     */
    public function getLoopIndex()
    {
        return $this->loopIndex;
    }
}
