<?php

namespace Macros\Bridges\Nette;

use Macros\MacroConfirm;
use Macros\MacroIfCurrent;
use Macros\MacroIfCurrentIn;
use Macros\MacroIfCurrentSwitch;
use Macros\MacroSubmitButton;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Macros\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'IfCurrent'       => false,
        'IfCurrentIn'     => false,
        'IfCurrentSwitch' => false,
        'SubmitButton'    => false,
        'Confirm'         => false,
    ];


    /**
     * Before Compile.
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        // load macro to latte
        $latteFactory = $builder->getDefinition('latte.latteFactory');
        if ($config['IfCurrent']) {
            $latteFactory->addSetup(MacroIfCurrent::class . '::install(?->getCompiler())', ['@self']);
        }
        if ($config['IfCurrentIn']) {
            $latteFactory->addSetup(MacroIfCurrentIn::class . '::install(?->getCompiler())', ['@self']);
        }
        if ($config['IfCurrentSwitch']) {
            $latteFactory->addSetup(MacroIfCurrentSwitch::class . '::install(?->getCompiler())', ['@self']);
        }
        if ($config['SubmitButton']) {
            $latteFactory->addSetup(MacroSubmitButton::class . '::install(?->getCompiler())', ['@self']);
        }
        if ($config['Confirm']) {
            $latteFactory->addSetup(MacroConfirm::class . '::install(?->getCompiler())', ['@self']);
        }
    }
}
