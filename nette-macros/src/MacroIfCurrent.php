<?php

namespace Macros;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


/**
 * Class MacroIfCurrent
 *
 * @author  geniv
 * @package Macros
 */
class MacroIfCurrent extends MacroSet
{

    /**
     * Register latte macros.
     *
     * @param Compiler $compiler
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('ifCurrent', [$me, 'macroIfCurrent'], '}');
    }


    /**
     * {ifCurrent destination [,] [params]}
     */
    public function macroIfCurrent(MacroNode $node, PhpWriter $writer)
    {
        if ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        }
        return $writer->write($node->args
            ? 'if ($this->global->uiPresenter->isLinkCurrent(%node.word, %node.array?)) {'
            : 'if ($this->global->uiPresenter->getLastCreatedRequestFlag("current")) {'
        );
    }
}
