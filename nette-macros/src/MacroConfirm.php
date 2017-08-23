<?php

namespace Macros;

use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Latte\MacroNode;
use Latte\Compiler;


/**
 * Class MacroConfirm
 *
 * @author  geniv, inspirated by Jan Brabec
 * @package Macros
 */
class MacroConfirm extends MacroSet
{

    /**
     * Register latte macros.
     *
     * @param Compiler $compiler
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('confirm', [$me, 'macroConfirm']);
    }


    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroConfirm(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('?> onclick="return confirm(%node.args);" <?php');
    }
}
