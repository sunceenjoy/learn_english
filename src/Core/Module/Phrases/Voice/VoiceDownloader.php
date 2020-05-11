<?php

namespace Eng\Core\Module\Phrases\Voice;

use Eng\Core\Exception\EngRuntimeException;
use Eng\Core\Module\Phrases\Voice\VoiceDownloadException;
use Eng\Core\Util\FileSystem;

class VoiceDownloader
{
    protected $vendors = array();
    protected $savePath = null;
    protected $log;
    protected $subPath = 'default';

    public function __construct($log, $savePath)
    {
        $this->log = $log;
        $this->savePath = $savePath;
    }

    public function addVendor(VoiceDownload $vendor)
    {
        $this->vendors[] = $vendor;
    }

    public function setSubPath($path)
    {
        $this->subPath = $path;
    }

    public function setSavePath($path)
    {
        $this->savePath = $path;
    }

    protected function getVoicePath(VoiceDownload $vendor, $phraseName)
    {
        $vendorName = strtolower($vendor->getVendor());
        $phraseName = str_replace('?', '', trim($phraseName));
        $phraseName = str_replace(' ', '-', $phraseName);
        return sprintf("%s/%s/%s.mp3", $this->subPath, $vendorName, $phraseName);
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

    public function downloadByVendor(VoiceDownload $vendor, $phraseName, $force)
    {
        if (! $vendor instanceof VoiceDownload) {
            throw new VoiceDownloadException("Please pass an instance of VoiceDownload as first parameter!");
        }

        try {
            $finalPath = $this->savePath.'/'.$this->getVoicePath($vendor, $phraseName);
            if (!$force && is_file($finalPath)) {
                return $this->getVoicePath($vendor, $phraseName);
            }

            FileSystem::mkdir(dirname($finalPath));
            $fileName = $vendor->downloadVoice($phraseName);

            if (copy($fileName, $finalPath)) {
                unlink($fileName);
                chmod($finalPath, 0777);
                return $this->getVoicePath($vendor, $phraseName);
            } else {
              $this->log->addWarning("Voice vendor %s copy failed");
            }
        } catch (VoiceDownloadException $e) {
            $this->log->addWarning(sprintf("Voice vendor %s doesn't work: %s", $vendor->getVendor(), $e->getMessage()));
        }
        return false;
    }

    public function download($phraseName, $force = false)
    {
        if (empty($this->vendors)) {
            throw new EngRuntimeException('Please add proper vendor first!');
        }

        /** @var \Eng\Core\Module\Phrases\Voice\VoiceDownload $vendor */
        foreach ($this->vendors as $vendor) {
            $finalPath = $this->downloadByVendor($vendor, $phraseName, $force);
            if ($finalPath != false) {
                return $finalPath;
            }
        }
        throw new VoiceDownloadException("No valid voice vendor!");
    }
}
