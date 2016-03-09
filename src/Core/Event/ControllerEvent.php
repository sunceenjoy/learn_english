<?php

namespace Eng\Core\Event;

use Symfony\Component\EventDispatcher\Event;

class ControllerEvent extends Event
{
    const PRE_ACTION = 1;
    const POST_ACTION = 2;

    private $c;

    public function __construct($c)
    {
        $this->c = $c;
        $this->c['dispather']->addListener(ControllerEvent::PRE_ACTION, array($this, 'preAction'));
    }

    public function preAction()
    {

    }
}
