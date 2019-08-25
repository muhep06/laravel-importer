<?php


namespace AcikVeri\Importer\JSONImporter;

use AcikVeri\Importer\Interfaces\Importer;
use GuzzleHttp\Client;
use Closure;
use Illuminate\Support\Facades\Schema;


class JSONImporter implements Importer
{
    public $json;
    public $index;
    private $include;
    private $table;

    /**
     * @param string $url
     * @param array $headers
     * @return $this
     */
    public function loadFromUrl(string $url, array $headers = [])
    {
        $client = new Client([
            'headers' => $headers
        ]);
        $this->json = json_decode($client->get($url)->getBody());
        return $this;
    }

    /**
     * @param string $json
     * @return $this
     */
    public function loadFromString(string $json) {

        $this->json = json_decode($json);
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

    public function insert(string $column, string $key) {
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
        $load = $this->json;
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
    public function import() {
        foreach ($this->include as $tableName=>$tables) {
            $i = 1;
            $model = new $tableName();
            $relation = new JSONImporterRelation($model, $this, $this->json, $i);
            foreach ($this->get($this->index) as $jsonKey=>$index) {
                if (is_array($this->get($this->index))) {
                    $model = new $tableName();
                    $relation->model = $model;
                    $relation->index = $i;
                    foreach ($index as $key => $path) {
                        foreach ($tables as $column => $item) {
                            if ($column !== 'relation') {
                                if ($key == $item) {
                                    $model->{$column} = $path;
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
                } else {
                    foreach ($tables as $column=>$item) {
                        if ($column !== 'relation') {
                            if ($jsonKey == $item) {
                                $model->{$column} = $index;
                            }
                        } else {
                            if (is_array($item)) {
                                $return = $item['closure']($relation);
                                $model->{$item['column']} = $return;
                            }
                        }
                    }
                    $model->save();
                }
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
                            echo $path . '<br>';
                            echo $data[$column] . '<br>';
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
