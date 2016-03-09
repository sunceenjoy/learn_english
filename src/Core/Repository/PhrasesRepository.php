<?php

namespace Eng\Core\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

class PhrasesRepository extends BaseRepository
{

    /**
     * usage: http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/native-sql.html
     */
    public function getQuizList($status, $amount)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('Eng:PhrasesEntity', 'w');

        // Limit parameter can not be parameterized
        $query = $this->_em->createNativeQuery("SELECT *, (success - failure) seq FROM phrases w WHERE w.status = :status ORDER BY seq ASC, failure DESC, create_time ASC LIMIT ". intval($amount), $rsm);
        $query->setParameter('status', $status, \PDO::PARAM_INT);
        $words = $query->getArrayResult();
        return $words;
    }
}
