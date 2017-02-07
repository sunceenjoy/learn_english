<?php
namespace Eng\Web\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Description of words
 *
 * @author grantsun
 */
class AuthController extends BaseController
{
    public function login()
    {
        return $this->render('web/login.html.twig');
    }
    
    public function postLogin(Request $request)
    {
        if ($this->c['auth']->handle($request)) {
            $this->session->set('isTester', true);
            return new RedirectResponse('/');
        }
        
        $this->session->getFlashBag()->add('error', 'Invalid username or password!');
        $this->session->set('isTester', true);
        return new RedirectResponse('/login');
    }
    
    public function logout()
    {
        $this->c['auth']->earseAuthenticatedUser();
        $this->session->remove('isTester');
        return new RedirectResponse('/login');
    }
}
