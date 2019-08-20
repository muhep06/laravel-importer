<?php


namespace AcikVeri\Importer\XMLImporter;


use GuzzleHttp\Client;

class XMLImporter
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
        $this->data = simplexml_load_string($data);
        return $this;
    }
}