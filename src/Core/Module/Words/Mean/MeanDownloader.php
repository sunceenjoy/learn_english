<?php

namespace Eng\Core\Module\Words\Mean;

use Eng\Core\Module\Words\Mean\MeanDownload;
use Eng\Core\Module\Words\Mean\MeanDownloadException;

class MeanDownloader
{
    protected $vendors = array();

    protected $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function addVendor(MeanDownload $vendor)
    {
        $this->vendors[] = $vendor;
    }

    /**
     *
     * @param string $wordName
     * @return MeanDownload
     * @throws MeanDownloadException
     */
    public function download($wordName)
    {
        /** @var MeanDownload $vendor */
        foreach ($this->vendors as $vendor) {
            try {
                return $vendor->requestWord($wordName);
            } catch (MeanDownloadException $e) {
                $this->log->addWarning(sprintf("Mean Vendor %s doesn't work!", $vendor->getVendor()));
            }
        }

        throw new MeanDownloadException("No valid mean vendor to use");
    }
}
