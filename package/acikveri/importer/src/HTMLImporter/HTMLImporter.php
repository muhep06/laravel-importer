<?php


namespace AcikVeri\Importer\HTMLImporter;


use AcikVeri\Importer\Interfaces\Importer;
use Closure;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class HTMLImporter implements Importer
{
    public $html;
    public $index;
    private $include;
    private $table;

    public function loadFromUrl(string $url, array $headers = [])
    {
        $client = new Client([
            'headers' => $headers
        ]);
        $this->html = new Crawler((string)$client->get($url)->getBody());
        return $this;
    }

    public function loadFromString(string $data)
    {
        $this->html = new Crawler($data);
        return $this;
    }

    public function setModel(string $model)
    {
        $this->include[$model] = [];
        $this->table = $model;
        return $this;
    }

    public function insert(string $column, string $key)
    {
        $this->include[$this->table][$column] = $key;
        return $this;
    }

    public function get(string $path = null)
    {
        return $this->html->filter($path);
    }

    public function relation(string $column, Closure $callback)
    {
        $this->include[$this->table]['relation'] = [ 'column' => $column, 'closure' => $callback ];
        return $this;
    }

    public function import()
    {
        foreach ($this->include as $tableName=>$tables) {
            foreach ($this->html->filter($this->index)->children() as $child) {
                $model = new $tableName();
                $relation = new HTMLImporterRelation($model, $this, $this->html);
                foreach ($tables as $column => $item) {
                    $crawler = new Crawler($child);
                    if ($column !== 'relation') {
                        $model->{$column} = trim($crawler->filter($item)->text());
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

    public function update(int $start = 0)
    {
        foreach ($this->include as $tableName=>$tables) {
            foreach ($this->html->filter($this->index)->children() as $ke=>$child) {
                $model = new $tableName();
                foreach ($tables as $column => $item) {
                    $crawler = new Crawler($child);
                    if ($column !== 'relation') {
                        $data = $model->get()[$start + $ke];
                        $html = $crawler->filter($item)->text();
                        if ($data->{$column} != $html) {
                            $model->where('id', $data->id)->update([ $column => $html ]);
                        }
                    }
                }
            }
        }
    }
}
