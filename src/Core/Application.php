<?php

namespace Eng\Core;

use Eng\Core\Container;
use Eng\Core\Event\ControllerEvent;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;

/**
 * This can be considered the kernel of English's newer MVC web application.
 * Responsible for error handling requests, routing, exception/error handling.
 */
class Application implements HttpKernelInterface
{
    /**
     * Name of ESP Bundle, used to resolve controllers. Currently only 'Web' or 'WebAdmin'
     * @var string
     */
    protected $bundle;

    /**
     * DIC
     * @var Container
     */
    protected $container;

    /** @var \Eng\Core\Security\Auth $auth */
    protected $auth;
    
    public function __construct($bundle, Container $container)
    {
        $this->bundle = $bundle;
        $this->container = $container;
        $this->auth = $container['auth'];
    }

    /** @var \Eng\Core\Event[] $events */
    protected $events;

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->addEvents();
        
        try {
            $parameters = $this->route();

            if (!(isset($parameters['_controller']) && strpos($parameters['_controller'], 'Eng\Web\Controller\AuthController') === 0)) {
                if (!$this->container['auth']->isAuthenticated()) {
                    return new RedirectResponse('/login');
                }
            }
            
            list($controller, $action) = $this->resolveController($parameters);
                        
            // Add route parameters to the request
            $request->attributes->add($parameters);
            unset($parameters['_route']);
            unset($parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
        } catch (\Exception $e) {
            if ($catch) {
                $this->getErrorResponse('We can not find the page you were looking for.')->send();
                exit;
            } else {
                throw $e;
            }
        }
        
        $this->container['dispather']->dispatch(ControllerEvent::PRE_ACTION);

        if (($response = $controller->getResponse()) !== null) {
            return $response;
        }

        $controller->setResponse($controller->$action($this->container['request']));
        $this->container['dispather']->dispatch(ControllerEvent::POST_ACTION);

        return $controller->getResponse();
    }

    protected function route()
    {
        /*
         * Magic route added intentionally as the last route
         * No _controller parameter defined, will be resolved when matched
         * For example, /asdf/qwer == ESP\[bundle name]\Controller\AsdfController::querAction
         * and /device_test/sample_page == ESP\[bundle name]\Controller\DeviceTestController::samplePageAction
         */
        $this->container['router.routes']->add('magic', new Route('/{controller}/{action}'));

        return $this->container['router']->match($this->container['request']->getPathInfo());
    }

    protected function resolveController($parameters)
    {
        if ($parameters['_route'] === 'magic') {
            // Controller is not defined, magically resolve.
            $controller = sprintf(
                'Eng\%s\Controller\%sController::%sAction',
                $this->bundle,
                Inflector::classify(strtolower($parameters['controller'])),
                Inflector::camelize(strtolower($parameters['action']))
            );
        } else {
            // Controller to use defined explicitly in route parameters
            $controller = $parameters['_controller'];
        }

        list($controller, $action)  = $this->createController($controller);

        if (!method_exists($controller, $action)) {
            throw new \InvalidArgumentException(
                sprintf('Method "%s::%s" does not exist.', get_class($controller), $action)
            );
        }

        return array($controller, $action);
    }

    /**
     * Returns a callable for the given controller.
     * @param string $controller A Controller string
     * @return mixed A PHP callable
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $action) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return array(new $class($this->container, ''), $action);
    }

    protected function getErrorResponse($message = '', $status = 404)
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container['twig'];

        return new Response(
            $twig->render(
                'web/error.html.twig',
                array(
                    'main'            => $message,
                    'support_address' => $this->container['config']['support_address'],
                    'support_email'   => $this->container['config']['support_email'],
                    'support_phone'   => $this->container['config']['support_phone'],
                )
            ),
            $status
        );
    }

    public function addEvents()
    {
    }
}
