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
        $arr = $this->db->fetchArray("select * from words");
        print_r($arr);die;
    }

    public function getAction()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('status' => 1));
    }
}
