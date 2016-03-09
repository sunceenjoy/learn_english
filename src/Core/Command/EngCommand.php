<?php

namespace Eng\Core\Command;

use Eng\Core\Container;
use Symfony\Component\Console\Command\Command;

abstract class EngCommand extends Command
{
    /** @var Container $container */
    protected $c;
    protected $db;
    protected $em;

    public function __construct(Container $container)
    {
        parent::__construct();

        $this->c = $container;
        $this->db = $this->c['db.eng'];
        $this->em = $this->c['doctrine.entity_manager'];
    }
}
