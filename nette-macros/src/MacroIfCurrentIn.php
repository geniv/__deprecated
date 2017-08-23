<?php

namespace Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


/**
 * Class MacroIfCurrentIn
 *
 * @author  geniv
 * @package Macros
 */
class MacroIfCurrentIn extends MacroSet
{

    /**
     * Register latte macros.
     *
     * @param Compiler $compiler
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('ifCurrentIn', [$me, 'macroIfCurrentIn'], [$me, 'macroIfCurrentInEnd']);
    }


    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroIfCurrentIn(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('foreach (%node.array as $l) { if ($presenter->isLinkCurrent($l)) { $_c = true; break; }} if (isset($_c)): ');
    }


    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroIfCurrentInEnd(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('endif; unset($_c);');
    }
}
