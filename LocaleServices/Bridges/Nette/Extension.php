<?php

namespace LocaleServices\Bridges\Nette;

use Nette;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * nette extension pro zavadeni jazykove sluzby jako rozsireni
 *
 * @author  geniv
 * @package LocaleServices\Bridges\Nette
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

        switch ($config['source']) {
            case 'DevNull':
                $localeService = $builder->addDefinition($this->prefix('default'))
                    ->setClass('LocaleServices\Drivers\DevNull')
                    ->setInject(false);
                break;

            case 'Database':
                $localeService = $builder->addDefinition($this->prefix('default'))
                    ->setClass('LocaleServices\Drivers\Database', [$config['parameters']])
                    ->setInject(false);
                break;

            case 'Neon':
                $localeService = $builder->addDefinition($this->prefix('default'))
                    ->setClass('LocaleServices\Drivers\Neon', [$config['parameters']])
                    ->setInject(false);
                break;
        }

        // pokud je debugmod a existuje rozhranni tak aktivuje panel
        if ($builder->parameters['debugMode'] && interface_exists('Tracy\IBarPanel')) {
            $builder->addDefinition($this->prefix('panel'))
                ->setClass('LocaleServices\Bridges\Tracy\Panel');

            $localeService->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
        }
    }


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
//        $config = $this->getConfig();

        $applicationService = $builder->getByType('Nette\Application\Application') ?: 'application';
        if ($builder->hasDefinition($applicationService)) {
            $builder->getDefinition($applicationService)
                ->addSetup('$service->onRequest[] = ?', [[$this->prefix('@default'), 'onRequest']]);
        }
    }
}
