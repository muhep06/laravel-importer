<?php


namespace AcikVeri\Importer\XMLImporter;


use AcikVeri\Importer\Interfaces\Importer;
use AcikVeri\Importer\Models\DynamicModel;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;

class XMLImporter implements Importer
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
     * @param string $model
     * @return $this
     */
    public function setModel(string $model)
    {
        $this->include[$model] = [];
        $this->table = $model;
        return $this;
    }

    /**
     * @param $column
     * @param $key
     * @return $this
     */
    public function insert(string $column, string $key)
    {
        $this->include[$this->table][$column] = $key;
        return $this;
    }

    /**
     * @param $column
     * @param Closure $callback
     * @return $this
     */
    public function relation(string $column, Closure $callback)
    {
        $this->include[$this->table]['relation'] = [ 'column' => $column, 'closure' => $callback ];
        return $this;
    }

    public function get(string $path = null)
    {
        $load = $this->xml;
        if ($path == null) {
            return $load;
        }
        foreach (explode('.', $path) as $item) {
            $load = $load->{$item};
        }
        return $load;
    }

    /**
     * @param bool $fresh
     * @return void
     */
    public function import(bool $fresh = false) {
        foreach ($this->include as $tableName=>$tables) {
            if ($fresh) {
                Schema::disableForeignKeyConstraints();
                $tableName::truncate();
                Schema::enableForeignKeyConstraints();
            }
            $i = 1;
            foreach ($this->get($this->index) as $index) {
                $model = new $tableName();
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
                                $return = $item['closure']($relation);
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
            $model = new $tableName();
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
                            $model->where('id', $data['id'])->update([ $column => $path ]);
                        }
                    }
                }
            }
        }
    }
}
