<?php

namespace AliasRouter;

use Nette\Application\UI\Presenter;


/**
 * Class InternalRouterPresetner
 *
 * @author  geniv
 * @package AliasRouter
 */
class InternalRouterPresetner extends Presenter
{
    private $name;


    /**
     * InternalRouterPresetner constructor.
     *
     * @param       $name
     * @param       $action
     * @param array $parameters
     */
    public function __construct($name, $action, $parameters = [])
    {
        $this->name = $name;
        $this->params = $parameters;
        $this->params['action'] = $action;
    }


    /**
     * Presenter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Presenter action.
     *
     * @param bool $fullyQualified
     * @return mixed|string
     */
    public function getAction($fullyQualified = false)
    {
        return ($fullyQualified ? ':' . $this->getName() . ':' . $this->params['action'] : $this->params['action']);
    }
}
