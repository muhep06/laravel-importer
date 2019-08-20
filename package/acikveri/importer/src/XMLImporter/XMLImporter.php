<?php


namespace AcikVeri\Importer\XMLImporter;


use AcikVeri\Importers\Models\DynamicModel;
use GuzzleHttp\Client;

class XMLImporter
{
    private $data;
    private $index;
    private $include;
    private $table;

    public function __construct()
    {
        $this->include = [];
    }

    /*
     * @param string $url
     */
    public function loadXmlFromUrl($url) {
        $client = new Client();
        $this->data = $client->get($url)->getBody();
        return $this;
    }

    /*
     * @param string $data
     */
    public function loadXmlFromString($data)
    {
        $this->data = $data;
        return $this;
    }

    /*
     * @param string $key
     */
    public function setIndex(string $key)
    {
        $this->index = $key;
        return $this;
    }

    /*
     * @param string $table
     */
    public function setTable($table)
    {
        $this->include[$table] = [];
        $this->table = $table;
        return $this;
    }

    /*
     * @param string
     */

    public function insert($column, $key) {
        $this->include[$this->table][$column] = $key;
        return $this;
    }

    /*
     * @param string $column, $key
     */
    public function relation($column, \Closure $callback) {
        //$this->include[$this->table]['relation'] = [ 'column' => $column ];
        $this->include[$this->table]['relation'] = [ 'column' => $column, 'closure' => $callback ];
        return $this;
    }

    /**
     * @return array
     */
    public function getIncludes()
    {
        return $this->include;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    public function update()
    {
        $data = $this->include;
        $xml = simplexml_load_string($this->data);
        $indexes = $xml->xpath($this->index);

        foreach ($data as $tableName=>$table) {
            if (SchemaCreator::isEmpty($tableName)) {
                break;
            }
            $model = new DynamicModel();
            $model->setTable($tableName);
            foreach ($model->get() as $key=>$col) {
                foreach ($table as $itemName=>$item) {
                    if ($itemName !== 'relation') {
                        $path = $indexes[$key]->xpath($item)[0];
                        if ($path == "") {
                            $path = null;
                        }
                        if ($col[$itemName] !== $path) {
                            if ($path == "") {
                                $path = null;
                            }
                            $model->where('id', $col['id'])->update([ $item => $path ]);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /*
     * @param array $data
     */
    public function import()
    {
        $data = $this->include;
        $xml = simplexml_load_string($this->data);
        $indexes = $xml->xpath($this->index);
        foreach ($data as $tableName=>$table) {
            if (!SchemaCreator::isEmpty($tableName)) {
                break;
            }
            $i = 1;
            foreach ($indexes as $index) {
                $model = new DynamicModel();
                $model->setTable($tableName);
                $relation = new XMLParserRelation();
                $relation->setModel($model);
                $relation->setXml($xml);
                $relation->setParser($this);
                $relation->setLoopIndex($i);
                foreach ($index as $key=>$path) {
                    foreach ($table as $itemName=>$item) {
                        if ($itemName !== 'relation') {
                            if ($key == $item) {
                                if ($path != null && $path != '') {
                                    $model->{$itemName} = $path;
                                }
                            }
                        } else {
                            if (is_array($item)) {
                                $return = $item['closure'](new DynamicModel(), $relation);
                                if ($return !== null) {
                                    $model->{$item['column']} = $return;
                                } else {
                                    $model->{$item['column']} = $i;
                                }

                            }
                        }
                    }
                }
                $i++;
                $model->save();
            }
        }
    }
}
