<?php

namespace Eng\Core\Module\Words\Voice;

abstract class VoiceDownload
{
    protected $savePath = '/tmp';

    public function __construct($savePath = null)
    {
        if ($savePath !== null) {
            $this->savePath = $savePath;
        }

        $this->init();
    }

    public function setSavePath($path)
    {
        $this->savePath = $path;
    }

    public function getTmpName()
    {
        return tempnam($this->savePath, 'words');
    }

    public function download($url, $savePath)
    {
        // set_time_limit(0);
        $fp = fopen($savePath, 'w');
        $ch = curl_init(str_replace(" ", "%20", $url));
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $this->isAudio($savePath);
    }

    public function isAudio($file)
    {
        $allowed = array(
            'audio/mpeg', 'audio/x-mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'audio/aiff',
            'audio/mid', 'audio/x-aiff', 'audio/x-mpequrl','audio/midi', 'audio/x-mid',
            'audio/x-midi','audio/wav','audio/x-wav','audio/xm','audio/x-aac','audio/basic',
            'audio/flac','audio/mp4','audio/x-matroska','audio/ogg','audio/s3m','audio/x-ms-wax',
            'audio/xm'
        );

        // check REAL MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file);
        finfo_close($finfo);

        // check to see if REAL MIME type is inside $allowed array
        if (in_array($type, $allowed)) {
            return true;
        } else {
            return false;
        }
    }

    public function getHtml($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    abstract public function init();

    abstract public function getVendor();

    public function isMe($vendorName)
    {
        return strcasecmp($this->getVendor(), $vendorName) === 0;
    }

    /**
     * @return string file name
     */
    abstract public function downloadVoice($word);
}
