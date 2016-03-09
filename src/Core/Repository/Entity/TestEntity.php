<?php

namespace Eng\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Eng\Core\Repository\TestRepository")
 * @ORM\Table(name="test")
 */
class TestEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     *
     * @ORM\Column(type="json_array")
     */
    protected $json = array('status' => 0);

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getJson()
    {
        return $this->json;
    }

    public function setJson($json)
    {
        $this->json = $json;
    }

    // oneToMany
    // http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html

    // Map setting see here:
    // http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#property-mapping
}
