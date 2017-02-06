<?php

namespace Eng\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Eng\Core\Repository\UsersRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class UserEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string",length=50) */
    protected $username;

    /** @ORM\Column(type="string",length=40) */
    protected $password;

    /** @ORM\Column(type="integer") */
    protected $gender;

    /** @ORM\Column(type="date") */
    protected $birthday;

    /** @ORM\Column(type="string", length=20) */
    protected $nickname;

    /** @ORM\Column(type="string", length=80) */
    protected $email;

    /** @ORM\Column(type="string", length=80) */
    protected $website;

    /** @ORM\Column(type="simple_array") */
    protected $roles;
    
    /** @ORM\Column(type="datetime") */
    protected $last_login_date = 0;

    /** @ORM\Column(type="integer",length=1) */
    protected $status;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }
    
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
    
    /** some others functions will be on later */
    
    /**
     * @ORM\PreFlush
     * see http://symfony.com/doc/current/cookbook/doctrine/file_uploads.html
     * see http://doctrine-orm.readthedocs.org/en/latest/reference/events.html#lifecycle-events
     */
    public function preFlush()
    {
        $this->last_login_date = new \Datetime('now');
    }
}
