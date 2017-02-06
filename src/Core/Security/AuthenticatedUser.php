<?php
namespace Eng\Core\Security;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticatedUser extends UsernamePasswordToken
{
    public function __construct($user, $credentials, $providerKey, $roles = array())
    {
        parent::__construct($user, $credentials, $providerKey, $roles);
    }
    
    public function isSuperUser()
    {
        return in_array('super_user', $this->getRoles());
    }
    
    public function isTester()
    {
        return in_array('tester', $this->getRoles());
    }
}
