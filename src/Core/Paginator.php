<?php

namespace Eng\Core;

use Doctrine\ORM\Tools\Pagination\Paginator as ParentPaginator;

/**
 * Description of Paginator
 *
 * @author grantsun
 */
class Paginator extends ParentPaginator
{
    private $currentPage;

    private $totalPage;

    private $totalItems;

    private $firstPage;

    private $lastPage;

    private $pageSize;

    public function __construct(\Doctrine\ORM\AbstractQuery $query, $currentPage, $pageSize, $showNumbers = 5, $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);

        $query->setFirstResult($pageSize * ($currentPage - 1)) // set the offset
            ->setMaxResults($pageSize); // set the limit;

        $this->pageSize = $pageSize;
        $this->totalItems = count($this);
        $this->totalPage = ceil($this->totalItems / $query->getMaxResults());
        $this->currentPage = max(1, min($this->totalPage, $currentPage));
        $this->firstPage = 1 + intval(($this->currentPage - 1) / $showNumbers) * $showNumbers;
        $this->lastPage = min($this->firstPage + $showNumbers -1, $this->totalPage);
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getTotalPage()
    {
        return $this->totalPage;
    }

    public function getFirstPage()
    {
        return $this->firstPage;
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function getTotalItems()
    {
        return $this->totalItems;
    }
}
