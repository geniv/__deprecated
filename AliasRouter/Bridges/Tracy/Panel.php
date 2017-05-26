<?php

namespace AliasRouter\Bridges\Tracy;

use AliasRouter\Model;
use AliasRouter\Router;
use Latte\Engine;
use Locale\Locale;
use Nette\Application\Application;
use Nette\DI\Container;
use Nette\SmartObject;
use Tracy\Debugger;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package AliasRouter\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    private $model;
    private $container;


    /**
     * Panel constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * Register to Tracy.
     *
     * @param Model $model
     */
    public function register(Model $model)
    {
        $this->model = $model;
        Debugger::getBar()->addPanel($this);
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return '<span title="Alias router"><img width="16px" height="16px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgZGF0YS1uYW1lPSJMYXllciAxNSIgaWQ9IkxheWVyXzE1IiB2aWV3Qm94PSIwIDAgMTAwIDEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48dGl0bGUvPjxwYXRoIGQ9Ik03Ni45Miw2MEg3MC4zNWw2LjgyLTM4LjIxTDYzLjc0LDYwSDM2LjI2TDIyLjMxLDE5Ljc4LDI5LjY1LDYwSDIzLjA4YTEwLjUsMTAuNSwwLDEsMCwwLDIxSDc2LjkyYTEwLjUsMTAuNSwwLDEsMCwwLTIxWk03Mi43OSwzOC4zNyw2OC4zLDYwSDY1Ljg0Wm0tNDUuNTgsMEwzNC4xNiw2MEgzMS43Wk03Ni45Miw3OUg3NFY3N0g3MnYySDYyVjc3SDYwdjJINTFWNzdINDl2MkgzOVY3N0gzN3YySDI4Vjc3SDI2djJIMjMuMDhhOC41LDguNSwwLDEsMSwwLTE3SDc2LjkyYTguNSw4LjUsMCwxLDEsMCwxN1pNNTYsNzBINjZWNjRINTZabTItNGg2djJINThabTExLDRINzlWNjRINjlabTItNGg2djJINzFaIi8+PC9zdmc+" />' .
            'Alias router' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $locale = $this->container->getByType(Locale::class);   // nacteni lokalizacni sluzby
        $application = $this->container->getByType(Application::class);    // nacteni aplikace
        $presenter = $application->getPresenter();

        $params = [
            'routerClass' => get_class($this->model),
            'routes'      => $this->model->getRouterAlias($presenter, $locale->getId()),
        ];

        $latte = new Engine;
        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);
    }
}
