<?php


namespace AcikVeri\Importer\XMLImporter;


use AcikVeri\Importer\Models\DynamicModel;
use Closure;
use GuzzleHttp\Client;

class XMLImporter
{
    public $xml;
    public $index;
    private $include;
    private $table;

    /**
     * @param string $url
     * @return $this
     */
    public function loadFromUrl(string $url)
    {
        $client = new Client();
        $this->xml = simplexml_load_string($client->get($url)->getBody());
        return $this;
    }

    /**
     * @param string $xml
     * @return $this
     */
    public function loadFromString(string $xml)
    {
        $this->xml = simplexml_load_string($xml);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->include[$table] = [];
        $this->table = $table;
        return $this;
    }

    /**
     * @param $column
     * @param $key
     * @return $this
     */
    public function insert($column, $key)
    {
        $this->include[$this->table][$column] = $key;
        return $this;
    }

    /**
     * @param $column
     * @param Closure $callback
     * @return $this
     */
    public function relation($column, Closure $callback)
    {
        $this->include[$this->table]['relation'] = [ 'column' => $column, 'closure' => $callback ];
        return $this;
    }

    public function get(string $path)
    {
        $load = $this->xml;
        foreach (explode('.', $path) as $item) {
            $load = $load->{$item};
        }
        return $load;
    }

    /**
     * @return void
     */
    public function import() {
        foreach ($this->include as $tableName=>$tables) {
            $i = 1;
            foreach ($this->get($this->index) as $index) {
                $model = new DynamicModel();
                $model->setTable($tableName);
                $relation = new XMLImporterRelation($model, $this, $this->xml, $i);
                foreach ($index as $key=>$path) {
                    foreach ($tables as $column=>$item) {
                        if ($column !== 'relation') {
                            if ($key == $item) {
                                if ($path != null && $path != '') {
                                    $model->{$column} = $path;
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

    /**
     * @return void
     */
    public function update()
    {
        foreach ($this->include as $tableName=>$tables) {
            $model = new DynamicModel();
            $model->setTable($tableName);
            foreach ($model->get() as $key=>$data) {
                foreach ($tables as $column=>$item) {
                    if ($column !== 'relation') {
                        $path = $this->get($this->index)[$key]->{$item};
                        if ($path == "") {
                            $path = null;
                        }
                        if ($data[$column] !== $path) {
                            if ($path == "") {
                                $path = null;
                            }
                            $model->where('id', $data['id'])->update([ $item => $path ]);
                        }
                    }
                }
            }
        }
    }
}
