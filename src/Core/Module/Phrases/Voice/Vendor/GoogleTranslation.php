<?php

namespace Eng\Core\Module\Phrases\Voice\Vendor;

use Eng\Core\Module\Phrases\Voice\VoiceDownload;
use Eng\Core\Module\Phrases\Voice\VoiceDownloadException;

class GoogleTranslation extends VoiceDownload
{
    private $api = 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key=';

    function __construct($savePath = null, $apiKey) {
        parent::__construct($savePath);
        $this->api = $this->api.$apiKey;
    }
    
    public function init()
    {
        
    }

    public function getVendor()
    {
        return 'GoogleTranslation';
    }

    private function sendRequest($word) {
        // Api reference:
        // https://cloud.google.com/text-to-speech/docs/reference/rest/v1beta1/text/synthesize
        // https://cloud.google.com/text-to-speech/docs/base64-decoding
        $ch = curl_init($this->api);
        $word = substr($word, 0, 100);

        $data = array(
          'input' => [
              'text' => $word
          ],
          'voice' => [
            'languageCode' => 'en-us',
            'ssmlGender' => 'FEMALE'
          ],
          'audioConfig' => [
            'audioEncoding' => 'MP3'
          ]
        );
        $data_string = json_encode($data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return json_decode($result);
    }

    public function downloadVoice($wordName)
    {
        $savePath = $this->getTmpName();
        $json = $this->sendRequest($wordName);
        if ($json) {
            file_put_contents($savePath, base64_decode($json->audioContent));
            return $savePath;
        }

        throw new VoiceDownloadException($this->getVendor()." can not download: ". $wordName);
    }
}
