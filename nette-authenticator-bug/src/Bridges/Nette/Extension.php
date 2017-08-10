<?php

namespace Authenticator\Bridges\Nette;

use Authenticator\DatabaseAuthenticator;
use Authenticator\LoginForm;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Authenticator\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array vychozi hodnoty */
    private $defaults = [
        'tablePrefix' => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $builder->addDefinition($this->prefix('default'))
            ->setClass(DatabaseAuthenticator::class, [$config]);

        $builder->addDefinition($this->prefix('form'))
            ->setClass(LoginForm::class);
    }
}
