<?php


namespace AcikVeri\Importer\JSONImporter;


use AcikVeri\Importer\Interfaces\Importer;
use GuzzleHttp\Client;


class JSONImporter implements Importer
{
    private $data;

    /**
     * @param $url
     * @return $this
     */
    public function loadFromUrl($url)
    {
        $client = new Client();
        $this->data = $client->get($url);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function loadFromString($data) {
        $this->data = json_decode($data);
        return $this;
    }
}