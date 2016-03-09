<?php

namespace Eng\Core\Module\Words\Mean\Vendor;

use Eng\Core\Module\Words\Mean\MeanDownload;
use Eng\Core\Module\Words\Mean\MeanDownloadException;

class YouDao extends MeanDownload
{

    private $urlFormat = 'http://fanyi.youdao.com/openapi.do?keyfrom=mmyyabb&key=1189677985&type=data&doctype=json&version=1.1&q=%s';

    private $data = null;

    public function init()
    {

    }

    public function getVendor()
    {
        return 'YouDao';
    }

    /**
     *
     * @return string
     */
    public function getPhonetic()
    {
        if (isset($this->data['basic']['us-phonetic'])) {
            return $this->data['basic']['us-phonetic'];
        }
        return isset($this->data['basic']['phonetic']) ?
                $this->data['basic']['phonetic'] :
                '';
    }

    /**
     *
     * @return array meaning
     */
    public function getMeaning()
    {
        return isset($this->data['basic']['explains']) ?
                $this->data['basic']['explains'] :
                array();
    }

    public function requestWord($wordName)
    {
        $url = sprintf($this->urlFormat, $wordName);
        $content = $this->getInfo($url);
        $this->data = json_decode($content, true);

        if (!$this->data || $this->data['errorCode'] != 0) {
            throw new MeanDownloadException($this->getVendor()." get Data error!");
        }

        $m = $this->getMeaning();
        $n = $this->getPhonetic();

        if (empty($m) && empty($n)) {
            throw new MeanDownloadException($this->getVendor()." both Meaning and Phonetic are empty!");
        }

        return $this;
    }
}
