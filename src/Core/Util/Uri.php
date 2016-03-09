<?php

namespace Eng\Core\Util;

use Eng\Core\Container;
use Symfony\Component\Routing\Generator\UrlGenerator;
class Uri
{
    private $c;
    private $request;

    public function __construct(Container $c)
    {
        $this->c = $c;
        $this->request = $c['request'];
    }

    public function getFullUrl()
    {
        return $this->request->getUri();
    }

    public function getHost()
    {
        return $this->request->getSchemeAndHttpHost();
    }

    public function getControllerName()
    {
        $key = $this->request->attributes->get('_route') == 'magic' ? 'controller' : '_route';
        return $this->request->attributes->get($key);
    }

    public function getActionName()
    {
        return $this->request->attributes->get('action');
    }

    public function replaceUrl($params = array())
    {
        $url = $this->request->getSchemeAndHttpHost().$this->request->getBaseUrl().$this->request->getPathInfo();
        $array = $this->request->query->all();
        unset($array[$this->getControllerName().'/'.$this->getActionName()]);
        $params = array_merge($array, $params);
        if (!empty($params)) {
            $url = $url.'?'.http_build_query($params);
        }
        return $url;
    }

    public function router($controller = null, $action = null, $params = array())
    {
        if (!isset($controller)) {
            $controller = $this->getControllerName();
        }
        if (!isset($action)) {
            $action = $this->getActionName();
        }
        $url = $this->getHost().'/'.$controller.'/'.$action;
        if (!empty($params)) {
            $url = $url.'?'.http_build_query($params);
        }
        return $url;
    }
}
