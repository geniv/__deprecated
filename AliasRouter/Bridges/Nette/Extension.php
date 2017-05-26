<?php

namespace AliasRouter\Bridges\Nette;

use AliasRouter\Bridges\Tracy\Panel;
use AliasRouter\Filter;
use AliasRouter\Model;
use Nette\DI\CompilerExtension;
use Tracy\IBarPanel;


/**
 * Class Extension
 *
 * nette extension pro alias router jako rozsireni
 *
 * @author  geniv
 * @package AliasRouter\Bridges\Nette
 */
class Extension extends CompilerExtension
{

    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        // nacteni modelu
        $aliasRouter = $builder->addDefinition($this->prefix('default'))
            ->setClass(Model::class, [$config['parameters']])
            ->setInject(false);

        // nacteni filteru
        $builder->addDefinition($this->prefix('filter'))
            ->setClass(Filter::class)
            ->setInject(false);

        // pripojeni filru na vkladani slugu
        $latteFactoryService = $builder->getByType(ILatteFactory::class) ?: 'nette.latteFactory';
        if ($builder->hasDefinition($latteFactoryService)) {
            $latte = $builder->getDefinition($latteFactoryService);
            $latte->addSetup('addFilter', ['addSlug', $this->prefix('@filter')]);
        }

        // pokud je debugmod a existuje rozhranni tak aktivuje panel
        if ($builder->parameters['debugMode'] && interface_exists(IBarPanel::class)) {
            $builder->addDefinition($this->prefix('panel'))
                ->setClass(Panel::class);

            $aliasRouter->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
        }
    }
}
