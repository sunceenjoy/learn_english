<?php

namespace Eng\Core\Module\Words\Voice\Vendor;

use Eng\Core\Module\Words\Voice\VoiceDownload;
use Eng\Core\Module\Words\Voice\VoiceDownloadException;

class Merriam extends VoiceDownload
{
    private $urlFormat = 'http://www.merriam-webster.com/audio.php?file=%s0001';

    public function init()
    {

    }

    public function getVendor()
    {
        return 'Merriam';
    }

    public function downloadVoice($wordName)
    {
        $url = sprintf($this->urlFormat, $wordName);
        $content = $this->getHtml($url);

        if (!empty($content) && preg_match('|<embed SRC="([^"]+)"|', $content, $match) > 0) {
            $voiceUrl = $match[1];
            $savePath = $this->getTmpName();
            if ($this->download($voiceUrl, $savePath)) {
                return $savePath;
            }
        }
        throw new VoiceDownloadException($this->getVendor()." can not download file:". $url);
    }
}
