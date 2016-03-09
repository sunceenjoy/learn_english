<?php

namespace Eng\Core\Repository\Entity;

abstract class BaseEntity
{
    public function fromArray($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray($c)
    {
        // http://symfony.com/doc/current/components/serializer.html
        $array = $c['entity.serializer']->normalize($this);
        unset($array['create_date'], $array['update_date'], $array['user_id']);
        return $array;
    }
}
