<?php

namespace Seo\Bridges\Nette;

use Nette\DI\CompilerExtension;
use Seo\FilterDescription;
use Seo\FilterTitle;
use Seo\Seo;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Seo\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
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

        // definition seo
        $builder->addDefinition($this->prefix('default'))
            ->setClass(Seo::class, [$config]);

        // definition filter title
        $builder->addDefinition($this->prefix('filter.title'))
            ->setClass(FilterTitle::class);

        // definition filter description
        $builder->addDefinition($this->prefix('filter.description'))
            ->setClass(FilterDescription::class);
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        // pripojeni filru do latte
        $builder->getDefinition('latte.latteFactory')
            ->addSetup('addFilter', ['seoTitle', $this->prefix('@filter.title')])
            ->addSetup('addFilter', ['seoDescription', $this->prefix('@filter.description')]);
    }
}
