<?php

namespace Eng\Core\Module\Words\Voice\Vendor;

use Eng\Core\Module\Words\Voice\VoiceDownload;
use Eng\Core\Module\Words\Voice\VoiceDownloadException;

class YouDao extends VoiceDownload
{
    private $urlFormat = 'http://dict.youdao.com/dictvoice?audio=%s&type=2';

    public function init()
    {

    }

    public function getVendor()
    {
        return 'YouDao';
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
