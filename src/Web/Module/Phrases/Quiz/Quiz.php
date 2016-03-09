<?php

namespace Eng\Web\Module\Phrases\Quiz;

use Eng\Core\Repository\Entity\PhrasesEntity;
use Eng\Core\Repository\Entity\PhrasesMarkerEntity;
use Eng\Core\Exception\EngRuntimeException;

class Quiz
{
    private $marker;

    public function __construct(PhrasesMarkerEntity $marker)
    {

        $this->marker = $marker;
    }

    public function doKnowUpdate(PhrasesEntity $word)
    {
        // invalid setting
        if ($word->getStatus() != $this->marker->getStatus()) {
            throw new EngRuntimeException("status doesn't match!");
        }

        $newSuccessCount = $word->getSuccess() + 1;

        if ($newSuccessCount >= $this->marker->getSuccess()) {
            $newStatus = $this->getNextStatusWhenSuccess($word->getStatus());
            if ($newStatus != $word->getStatus()) {
                $word->setStatus($newStatus);
                $word->setSuccess(0);
                $word->setFailure(0);
            } else {
                $word->setSuccess($newSuccessCount);
            }
        } else {
            $word->setSuccess($newSuccessCount);
        }
    }

    public function doUnKnowUpdate(PhrasesEntity $word)
    {
        // invalid setting
        if ($word->getStatus() != $this->marker->getStatus()) {
            throw new EngRuntimeException("status doesn't match!");
        }

        $newFailureCount = $word->getFailure() + 1;

        if ($newFailureCount >= $this->marker->getFailure()) {
            $newStatus = $this->getNextStatusWhenFail($word->getStatus());
            if ($newStatus != $word->getStatus()) {
                $word->setStatus($newStatus);
                $word->setSuccess(0);
                $word->setFailure(0);
            } else {
                 $word->setFailure($newFailureCount);
            }
        } else {
            $word->setFailure($newFailureCount);
        }
    }

    private function getNextStatusWhenSuccess($status)
    {
        if ($status == PhrasesEntity::MEDIUM) {
            return PhrasesEntity::EASY;
        }

        if ($status == PhrasesEntity::DIFFICULT) {
            return PhrasesEntity::MEDIUM;
        }

        if ($status == PhrasesEntity::NEWONE) {
            return PhrasesEntity::DIFFICULT;
        }

        // easy, all, rare
        return $status;
    }

    private function getNextStatusWhenFail($status)
    {
        if ($status == PhrasesEntity::EASY) {
            return PhrasesEntity::MEDIUM;
        }

        if ($status == PhrasesEntity::MEDIUM) {
            return PhrasesEntity::DIFFICULT;
        }

        if ($status == PhrasesEntity::DIFFICULT) {
            return PhrasesEntity::NEWONE;
        }

        // all, area, new
        return $status;
    }
}
