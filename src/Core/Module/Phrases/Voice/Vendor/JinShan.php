<?php

namespace Eng\Core\Module\Phrases\Voice\Vendor;

use Eng\Core\Module\Phrases\Voice\VoiceDownload;
use Eng\Core\Module\Phrases\Voice\VoiceDownloadException;

class JinShan extends VoiceDownload
{
    private $urlFormat = 'http://dict-co.iciba.com/api/dictionary.php?key=81E59E429D8B728EE0A974AEDE3DD281&type=json&w=%s';
    public function init()
    {
    }

    public function getVendor()
    {
        return 'JinShan';
    }

    public function downloadVoice($wordName)
    {
        $url = sprintf($this->urlFormat, $wordName);
        $json = json_decode($this->getHtml($url), true);
        if (!$json) {
            throw new MeanDownloadException($this->getVendor()." get Data error");
        }
        $voiceUrl = isset($json['symbols'][0]['ph_am_mp3']) ? $json['symbols'][0]['ph_tts_mp3'] : '';

        $savePath = $this->getTmpName();

        if (!empty($voiceUrl) && $this->download($voiceUrl, $savePath)) {
            return $savePath;
        }

        throw new VoiceDownloadException($this->getVendor()." can not download file ". $voiceUrl);
    }
}
