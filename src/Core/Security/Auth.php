<?php
namespace Eng\Core\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Session\Session;

class Auth
{
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * Session $session;
     */
    private $session;
    
    /**
     * @var string Uniquely identifies the secured area
     */
    private $providerKey = 'mmyyabb';

    /** @var AuthenticatedUser $authenticatedUser; */
    private $authenticatedUser = null;
    
    public function __construct(Session $session, DaoAuthenticationProvider $authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
        $this->session = $session;
        
        $user = $this->session->get('authUser');
        if (is_object($user)) {
            $this->setAuthenticatedUserByUsername($user);
        }
    }
    
    public function isAuthenticated()
    {
        return $this->authenticatedUser != null && $this->authenticatedUser->isAuthenticated();
    }
    
    public function handle(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );

        try {
            $authenticatedToken = $this
                ->authenticationManager
                ->authenticate($unauthenticatedToken);
        } catch (AuthenticationException $failed) {
            return false;
        }
        
        $this->setAuthenticatedUserByUsername($authenticatedToken->getUser());
        $this->session->set('authUser', $authenticatedToken->getUser());
        return true;
    }
    
    private function setAuthenticatedUserByUsername($user)
    {
        $this->authenticatedUser = new AuthenticatedUser($user, '', $this->providerKey, $user->getRoles());
    }
    
    public function getAuthenticatedUser()
    {
        return $this->isAuthenticated() ? $this->authenticatedUser : null;
    }
    
    public function earseAuthenticatedUser()
    {
        $this->session->remove('authUser');
        $this->authenticatedUser = null;
    }
}
