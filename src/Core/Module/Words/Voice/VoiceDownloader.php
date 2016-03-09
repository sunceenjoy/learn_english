<?php

namespace Eng\Core\Module\Words\Voice;

use Eng\Core\Exception\EngRuntimeException;
use Eng\Core\Module\Words\Voice\VoiceDownloadException;
use Eng\Core\Util\FileSystem;

class VoiceDownloader
{
    protected $vendors = array();
    protected $savePath = null;
    protected $log;

    public function __construct($log, $savePath)
    {
        $this->log = $log;
        $this->savePath = $savePath;
    }

    public function addVendor(VoiceDownload $vendor)
    {
        $this->vendors[] = $vendor;
    }

    protected function getVoicePath(VoiceDownload $vendor, $wordName)
    {
        $vendorName = $vendor->getVendor();
        $wordName = trim($wordName);
        return sprintf("%s/%s/%s.mp3", strtolower($vendorName), strtolower($wordName[0]), $wordName);
    }

    public function searchNextVendor($currentVendorName)
    {
        foreach ($this->vendors as $index => $vendor) {
            if ($vendor->isMe($currentVendorName)) {
                if ($index == count($this->vendors) - 1) {
                    return $this->vendors[0];
                } else {
                    return $this->vendors[$index + 1];
                }
            }
        }
        return $this->vendors[0];
    }

    public function downloadByVendor(VoiceDownload $vendor, $wordName, $force)
    {
        if (! $vendor instanceof VoiceDownload) {
            throw new VoiceDownloadException("Please pass an instance of VoiceDownload as first parameter!");
        }

        try {
            $finalPath = $this->savePath.'/'.$this->getVoicePath($vendor, $wordName);
            if (!$force && is_file($finalPath)) {
                return $this->getVoicePath($vendor, $wordName);
            }

            FileSystem::mkdir(dirname($finalPath));
            $fileName = $vendor->downloadVoice($wordName);

            if (copy($fileName, $finalPath)) {
                unlink($fileName);
                chmod($finalPath, 0777);
                return $this->getVoicePath($vendor, $wordName);
            }
        } catch (VoiceDownloadException $e) {
            $this->log->addWarning(sprintf("Voice vendor %s doesn't work!", $vendor->getVendor()));
        }
        return false;
    }

    public function download($wordName, $force = false)
    {
        if (empty($this->vendors)) {
            throw new EngRuntimeException('Please add proper vendor first!');
        }
        /** @var \Eng\Core\Module\Words\Voice\VoiceDownload $vendor */
        foreach ($this->vendors as $vendor) {
            $finalPath = $this->downloadByVendor($vendor, $wordName, $force);
            if ($finalPath != false) {
                return $finalPath;
            }
        }
        throw new VoiceDownloadException("No valid voice vendor!");
    }
}
