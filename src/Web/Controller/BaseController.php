<?php
namespace Eng\Web\Controller;

use Eng\Core\Container;
use Eng\Core\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    protected $template = 'web/partials/main.html.twig';

    /** @var \Symfony\Component\HttpFoundation\Response $response */
    protected $response = null;

    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request  = null;

    /** @var \Doctrine\DBAL\Connection $db */
    protected $db = null;

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em = null;

    protected $log = null;

    public function __construct(Container $container, $title = '')
    {
        $this->c = $this->container = $container;
        $this->request = $this->container['request'];
        $this->db = $this->container['db.eng'];
        $this->em = $this->container['doctrine.entity_manager'];
        $this->log = $this->container['log.main'];

        $this->container['dispather']->addListener(ControllerEvent::PRE_ACTION, array($this, 'preAction'));
        $this->container['dispather']->addListener(ControllerEvent::POST_ACTION, array($this, 'postAction'));
    }

    /**
     * Used temporarily to expose use of non-existent class properties
     * like tech,account_adder,superuser,tester
     */
    public function __get($name)
    {
        throw new \Exception("Non-existent class property on Geotrax_Page::".$name);
    }

    /**
     * User should not be here redirect to main tracker
     */
    public function rejectAdminAccess()
    {
        header('Location: '.$this->container['config']['base_url'].'/tracker/index.php?reset=1');
        exit;
    }

    /**
     * Renders a view via twig.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->container['twig']->render($view, $parameters));

        return $response;
    }

    /**
     * Called after constructor so controller classes do not have to override the constructor
     */
    protected function initialize()
    {

    }

    /**
     * Do access controal here, set response to stop from continuting actual action and postAction.
     */
    public function preAction()
    {

    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function postAction()
    {
        if ($this->response instanceof Response) {
            return;
        }

        if (is_string($this->response)) {
            $this->response = new Response($this->response);
            return;
        }

        $this->response = $this->render($this->template);
    }

    public function t($string)
    {
        return $this->container['translator']->trans($string);
    }
}
