<?php

namespace Eng\Core\Repository;

class UsersRepository extends BaseRepository
{
    public function getUserByUsername($username)
    {
        $user = $this->findOneBy(['username' => $username]);
        return $user;
    }
}
