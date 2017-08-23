<?php

namespace Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


/**
 * Class MacroIfCurrentSwitch
 *
 * @author  geniv
 * @package Macros
 */
class MacroIfCurrentSwitch extends MacroSet
{

    /**
     * Register latte macros.
     *
     * @param Compiler $compiler
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('ifCurrentSwitch', 'if (FALSE) {', '}');
        $me->addMacro('ifCurrentCase', '} elseif ($presenter->isLinkCurrent(%node.args)) {');
        $me->addMacro('ifCurrentDefault', '} else {');
    }
}
