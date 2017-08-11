<?php

namespace TracyClean\Bridges\Nette;

use Nette\DI\CompilerExtension;
use TracyClean\Bridges\Tracy\Panel;


/**
 * Class Extension
 *
 * @author  geniv
 * @package TracyClean\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'link' => [],
    ];


    /**
     * Before Compile.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $builder->addDefinition($this->prefix('panel'))
            ->setClass(Panel::class, [$config]);
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        // linked panel to tracy
        $builder->getDefinition('tracy.bar')
            ->addSetup('?->addPanel(?)', ['@self', $this->prefix('@panel')]);
    }
}
