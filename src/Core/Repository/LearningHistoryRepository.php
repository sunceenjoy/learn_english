<?php

namespace Eng\Core\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Eng\Core\Repository\Entity\LearningHistoryEntity;

class LearningHistoryRepository extends BaseRepository
{
    protected $types = array('words', 'phrases', 'prons');

    public function incrHistory($type, $status, $isSuccess)
    {
        if (!in_array($type, $this->types)) {
            return false;
        }
        $date = new \DateTime('now');
        $query = $this->_em->createQuery("SELECT w FROM Eng:LearningHistoryEntity w WHERE w.type = :type AND w.status = :status AND w.date = :date");
        $query->setParameter('status', $status);
        $query->setParameter('type', $type);
        $query->setParameter('date', $date->format('Y-m-d'));
        $history = $query->getOneOrNullResult();
        if (!$history) {
            $history = new LearningHistoryEntity();
            $history->setStatus($status);
            $history->setType($type);
            $history->setDate($date);
        }

        if ($isSuccess) {
            $history->incrSuccess();
        } else {
            $history->incrFail();
        }
        $this->_em->persist($history);
        $this->_em->flush();
        return true;
    }

    public function getHistory($type, $interval = 'day')
    {
        $query = $this->_em->createQuery("SELECT w FROM Eng:LearningHistoryEntity w WHERE w.type = :type ORDER BY w.date ASC");
        $query->setParameter('type', $type);
        $history = $query->getArrayResult();
        return $history;
    }
}
