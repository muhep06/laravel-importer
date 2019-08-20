<?php


namespace AcikVeri\Importer\XMLImporter;


use AcikVeri\Importer\Models\DynamicModel;
use SimpleXMLElement;

class XMLImporterRelation
{
    private $model;
    private $parser;
    private $xml;
    private $loopIndex;

    public function setModel(DynamicModel $model): void
    {
        $this->model = $model;
    }


    public function setParser(XMLImporter $parser): void
    {
        $this->parser = $parser;
    }


    public function setXml(SimpleXMLElement $xml): void
    {
        $this->xml = $xml;
    }

    /**
     * @return mixed
     */
    public function getModel(): DynamicModel
    {
        return $this->model;
    }

    /**
     * @return XMLImporter
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
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
