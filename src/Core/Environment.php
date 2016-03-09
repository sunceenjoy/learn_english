<?php

namespace Eng\Core;

use Eng\Core\Exception\EngRuntimeException;

class Environment
{
    /** @var String $env */
    private $env;

    private $env_dev = array(
        'eng_dev'
    );

    private $env_prod = array(
        'eng_prod'
    );

    public function __construct($env)
    {
        if (!in_array($env, $this->env_dev) && !in_array($env, $this->env_prod)) {
            throw new EngRuntimeException("Please specify running environment: ENG_ENV!");
        }
        $this->env = $env;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function isDev()
    {
        return in_array($this->env, $this->env_dev);
    }

    public function isProd()
    {
        return in_array($this->env, $this->env_prod);
    }
}
