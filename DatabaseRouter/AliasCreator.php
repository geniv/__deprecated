<?php

namespace DatabaseRouter;

use DatabaseRouter\Model;
use Nette\Application\UI\Control;

/**
 * Class AliasCreator
 * komponenta zajistujici prenos aliasu do router tabulek z latte
 *
 * @author  geniv
 * @package NetteWeb
 */
class AliasCreator extends Control
{
    /** @var DatabaseRouterModel */
    private $databaseRouterModel = null;


    /**
     * AliasCreator constructor.
     *
     * @param \DatabaseRouter\Model $model
     */
    public function __construct(Model $model)
    {
        $this->databaseRouterModel = $model;
    }


    /**
     * render komponenty
     *
     * @param $alias
     */
    public function render($alias)
    {
        if ($this->databaseRouterModel && $alias) {
            $this->databaseRouterModel->insertAlias($this->presenter, $alias);
        }
    }
}
