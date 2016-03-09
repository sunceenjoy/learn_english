<?php

namespace Eng\Core\Module\Words\Voice\Vendor;

use Eng\Core\Module\Words\Voice\VoiceDownload;
use Eng\Core\Module\Words\Voice\VoiceDownloadException;

class CamBridge extends VoiceDownload
{
    private $urlFormat = 'http://dictionary.cambridge.org/us/media/english/us_pron/%s/%s/%s/%s.mp3';

    public function init()
    {

    }

    public function getVendor()
    {
        return 'CamBridge';
    }

    public function downloadVoice($wordName)
    {
        $voiceUrl = sprintf($this->urlFormat, $wordName[0], str_pad(substr($wordName, 0, 3), 3, '_'), str_pad(substr($wordName, 0, 5), 5, '_'), $wordName);

        $savePath = $this->getTmpName();
        if ($this->download($voiceUrl, $savePath)) {
            return $savePath;
        }

        throw new VoiceDownloadException($this->getVendor()." can not download file:". $voiceUrl);
    }
}
