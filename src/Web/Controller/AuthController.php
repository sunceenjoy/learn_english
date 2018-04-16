<?php
namespace Eng\Web\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Auth controller
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
            if ($this->c['auth']->getAuthenticatedUser()->isTester()) {
                $this->session->set('isTester', true);
            }
            return new RedirectResponse('/Words/newWord');
        }
        
        $this->session->getFlashBag()->add('error', 'Invalid username or password!');
        return new RedirectResponse('/login');
    }
    
    public function logout()
    {
        $this->c['auth']->earseAuthenticatedUser();
        $this->session->remove('isTester');
        return new RedirectResponse('/login');
    }
}
