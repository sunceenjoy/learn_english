<?php

namespace Eng\Web\Module\Words\Quiz;

use Eng\Core\Repository\Entity\WordsEntity;
use Eng\Core\Repository\Entity\WordsMarkerEntity;
use Eng\Core\Exception\EngRuntimeException;

class Quiz
{
    private $marker;

    public function __construct(WordsMarkerEntity $marker)
    {

        $this->marker = $marker;
    }

    public function doKnowUpdate(WordsEntity $word)
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

    public function doUnKnowUpdate(WordsEntity $word)
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
        if ($status == WordsEntity::MEDIUM) {
            return WordsEntity::EASY;
        }

        if ($status == WordsEntity::DIFFICULT) {
            return WordsEntity::MEDIUM;
        }

        if ($status == WordsEntity::NEWONE) {
            return WordsEntity::DIFFICULT;
        }

        // easy, all, rare
        return $status;
    }

    private function getNextStatusWhenFail($status)
    {
        if ($status == WordsEntity::EASY) {
            return WordsEntity::MEDIUM;
        }

        if ($status == WordsEntity::MEDIUM) {
            return WordsEntity::DIFFICULT;
        }

        if ($status == WordsEntity::DIFFICULT) {
            return WordsEntity::NEWONE;
        }

        // all, area, new
        return $status;
    }
}
