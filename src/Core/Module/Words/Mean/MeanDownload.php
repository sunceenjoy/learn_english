<?php

namespace Eng\Core\Module\Words\Mean;

abstract class MeanDownload
{
    public function __construct()
    {
        $this->init();
    }

    abstract public function getVendor();

    abstract public function getPhonetic();

    abstract public function getMeaning();

    abstract public function init();

    abstract public function requestWord($wordName);

    public function getInfo($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
