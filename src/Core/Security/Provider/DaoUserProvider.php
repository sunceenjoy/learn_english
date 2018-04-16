<?php

namespace Eng\Core\Security\Provider;

use Eng\Core\Repository\UsersRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DaoUserProvider implements UserProviderInterface
{
    /**
     * @var UsersRepository
     */
    private $userRepository;
    
    public function __construct(UsersRepository $usersRepository)
    {
        $this->userRepository = $usersRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->getUserByUsername($username);
        if (!$user) {
            throw new UsernameNotFoundException("Can not find username:". $username);
        }
        return new User($user->getUsername(), $user->getPassword(), $user->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
