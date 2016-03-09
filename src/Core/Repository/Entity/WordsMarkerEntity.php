<?php

namespace Eng\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Eng\Core\Repository\WordsMarkerRepository")
 * @ORM\Table(name="words_marker")
 */
class WordsMarkerEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="status",type="integer")
     */
    protected $status;

    /** @ORM\Column(type="integer") */
    protected $page = 0;

    /** @ORM\Column(type="string",length=20) */
    protected $word;

    /** @ORM\Column(type="integer") */
    protected $success = 0;

    /** @ORM\Column(type="integer") */
    protected $failure = 0;

    /** @ORM\Column(type="datetime") */
    protected $date_time;

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setword($word)
    {
        $this->word = $word;
    }

    public function getWord()
    {
        return $this->word;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function setFailure($failure)
    {
        $this->failure = $failure;
    }

    public function getFailure()
    {
        return $this->failure;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
