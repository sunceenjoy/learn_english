<?php

namespace Eng\Core\Module\Words\Mean\Vendor;

use Eng\Core\Module\Words\Mean\MeanDownload;
use Eng\Core\Module\Words\Mean\MeanDownloadException;

class JinShan extends MeanDownload
{

    private $urlFormat = 'http://dict-co.iciba.com/api/dictionary.php?key=81E59E429D8B728EE0A974AEDE3DD281&type=json&w=%s';

    private $data = null;

    public function init()
    {

    }

    public function getVendor()
    {
        return 'JinShan';
    }

    public function getPhonetic()
    {
        if (isset($this->data['symbols'][0]['ph_am'])) {
            return $this->data['symbols'][0]['ph_am'];
        }
        return isset($this->data['symbols'][0]['ph_en']) ?
                $this->data['symbols'][0]['ph_en'] :
                '';
    }

    public function getMeaning()
    {
        $means = array();

        foreach ($this->data['symbols'][0]['parts'] as $item) {
            $means[] = $item['part']. ' '.implode(';', $item['means']);
        }
        return $means;
    }

    public function requestWord($wordName)
    {
        $url = sprintf($this->urlFormat, $wordName);
        $content = $this->getInfo($url);
        $this->data = json_decode($content, true);

        if (!$this->data) {
            throw new MeanDownloadException($this->getVendor()." get Data error!");
        }

        $m = $this->getMeaning();
        $n = $this->getPhonetic();

        if (empty($m) && empty($n)) {
            throw new MeanDownloadException($this->getVendor(). " both Meaning and Phonetic are empty!");
        }

        return $this;
    }
}
