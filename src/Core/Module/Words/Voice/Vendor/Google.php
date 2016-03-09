<?php

namespace Eng\Core\Module\Words\Voice\Vendor;

use Eng\Core\Module\Words\Voice\VoiceDownload;
use Eng\Core\Module\Words\Voice\VoiceDownloadException;

class Google extends VoiceDownload
{
    private $urlFormat = 'http://ssl.gstatic.com/dictionary/static/sounds/de/0/%s.mp3';

    public function init()
    {

    }

    public function getVendor()
    {
        return 'Google';
    }

    public function downloadVoice($wordName)
    {
        $voiceUrl = sprintf($this->urlFormat, $wordName);

        $savePath = $this->getTmpName();
        if ($this->download($voiceUrl, $savePath)) {
            return $savePath;
        }

        throw new VoiceDownloadException($this->getVendor()." can not download file:". $voiceUrl);
    }
}
