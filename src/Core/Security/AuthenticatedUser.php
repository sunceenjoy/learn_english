<?php
namespace Eng\Core\Security;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticatedUser extends UsernamePasswordToken
{
    public function __construct($user, $credentials, $providerKey, $roles = array())
    {
        parent::__construct($user, $credentials, $providerKey, $roles);
    }
    
    private function hasRole($roleName)
    {
        foreach ($this->getRoles() as $role) {
            if ($roleName === $role->getRole()) {
                return true;
            }
        }
        return false;
    }

    public function isSuperUser()
    {
        return $this->hasRole('super_user');
    }
    
    public function isTester()
    {
        return $this->hasRole('tester');
    }
}
