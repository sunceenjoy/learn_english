<?php

namespace Eng\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Eng\Core\Repository\WordsRepository")
 * @ORM\Table(name="words")
 * @ORM\HasLifecycleCallbacks
 */
class WordsEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string",length=20) */
    protected $name;

    /** @ORM\Column(type="string",length=100) */
    protected $means;

    /** @ORM\Column(type="string",length=20) */
    protected $voice;

    /** @ORM\Column(type="string",length=20) */
    protected $pronunciation;

    /** @ORM\Column(type="integer") */
    protected $success = 0;

    /** @ORM\Column(type="integer") */
    protected $failure = 0;

    /** @ORM\Column(type="smallint") */
    protected $status = 0;

    /** @ORM\Column(type="datetime") */
    protected $create_time;

    /** @ORM\Column(type="datetime") */
    protected $update_time;

    const NEWONE = 0;
    const EASY = 1;
    const MEDIUM = 2;
    const DIFFICULT = 3;
    const RARE = 4;
    const ALL = -1;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setMeans($means)
    {
        $this->means = $means;
    }

    public function getMeans()
    {
        return $this->means;
    }

    public function setVoice($voice)
    {
        $this->voice = $voice;
    }

    public function getVoice()
    {
        return $this->voice;
    }

    public function setPronunciation($pronunciation)
    {
        $this->pronunciation = $pronunciation;
    }

    public function getPronunciation()
    {
        return $this->pronunciation;
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

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCreateTime($tz)
    {
        $this->create_time = $tz;
    }

    public function getCreateTime()
    {
        return $this->create_time;
    }

    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * @ORM\PreFlush
     * see http://symfony.com/doc/current/cookbook/doctrine/file_uploads.html
     * see http://doctrine-orm.readthedocs.org/en/latest/reference/events.html#lifecycle-events
     */
    public function preFlush()
    {
        $this->update_time = new \Datetime('now');
    }

    public function fromArray($array)
    {
        parent::fromArray($array);
        $this->setCreateTime(new \Datetime($this->getCreateTime()));
    }
}
