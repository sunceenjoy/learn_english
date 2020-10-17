<?php

namespace Eng\Core\Module\Phrases\Voice\Vendor;

use Eng\Core\Module\Phrases\Voice\VoiceDownload;
use Eng\Core\Module\Phrases\Voice\VoiceDownloadException;

class NaturalReaders extends VoiceDownload
{
    private $urlFormat = 'http://api.naturalreaders.com/v2/tts/?t=\'%s\'&r=11&s=1&requesttoken=%s';
    private $tokkenUrl = 'http://api.naturalreaders.com/v2/auth/requesttoken?callback=jQuery16206325808877591044_1446529793937&appid=pelc790w2bx&appsecret=2ma3jkhafcyscswg8wgk00w0kwsog4s&_=1446529872523';
    private $tokken = null;
    public function init()
    {
    }

    private function getToken() {
      $jsonString = strip_tags($this->getHtml($this->tokkenUrl));
      $jsonString = preg_replace('|^jQ.*\(|', '', $jsonString);
      $jsonString = preg_replace('|\);$|', '', $jsonString);
      $json = json_decode($jsonString, true);
      if (empty($json) || $json['rst'] != true) {
          throw new VoiceDownloadException($this->getVendor()." can not get tokken string!");
      }
      $this->tokken = $json['requesttoken'];
    }
    public function getVendor()
    {
        return 'NaturalReaders';
    }

    public function downloadVoice($wordName)
    {
      if (!$this->tokken) {
        $this->getToken();
      }
        $voiceUrl = sprintf($this->urlFormat, $wordName, $this->tokken);

        $savePath = $this->getTmpName();
        if ($this->download($voiceUrl, $savePath)) {
            return $savePath;
        }

        throw new VoiceDownloadException($this->getVendor()." can not download file ". $voiceUrl);
    }
}
