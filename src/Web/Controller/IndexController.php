<?php

namespace Eng\Web\Controller;

/**
 * Description of IndexController
 *
 * @author grantsun
 */
class IndexController extends BaseController
{
    public function index()
    {
    }

    public function getAction()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('status' => 1));
    }
}
