<?php

namespace Eng\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Eng\Core\Repository\LearningHistoryRepository")
 * @ORM\Table(name="learning_history")
 */
class LearningHistoryEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $type;

    /** @ORM\Column(type="integer") */
    protected $status;

    /** @ORM\Column(type="integer") */
    protected $success = 0;

    /** @ORM\Column(type="integer") */
    protected $fail = 0;

    /** @ORM\Column(type="date") */
    protected $date;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function incrSuccess()
    {
        $this->success += 1;
    }

    public function setFail($fail)
    {
        $this->fail = $fail;
    }

    public function getFail()
    {
        return $this->fail;
    }

    public function incrFail()
    {
        $this->fail += 1;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
}
