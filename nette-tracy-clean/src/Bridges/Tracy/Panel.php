<?php

namespace TracyClean\Bridges\Tracy;

use Exception;
use Latte\Engine;
use Nette\Application\Application;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\DI\Container;
use Nette\SmartObject;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package TracyClean\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    /** @var Container container from DI */
    private $container;
    /** @var array */
    private $parameters;


    /**
     * Panel constructor.
     *
     * @param array     $parameters
     * @param Container $container
     */
    public function __construct(array $parameters, Container $container)
    {
        $this->container = $container;

        $this->parameters = $parameters;
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return '<span title="Tracy clean"><img width="16px" height="16px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDQ4IDQ4IiBoZWlnaHQ9IjQ4cHgiIHZlcnNpb249IjEuMSIgdmlld0JveD0iMCAwIDQ4IDQ4IiB3aWR0aD0iNDhweCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9IkV4cGFuZGVkIj48Zz48Zz48cGF0aCBkPSJNNDQsMzJINHYtOGMwLTIuMjA2LDEuNzk0LTQsNC00aDEwLjA0NmMwLjMzMywwLDAuNjQyLTAuMTY0LDAuODI4LTAuNDM5czAuMjI0LTAuNjI0LDAuMTAxLTAuOTMyICAgICBDMTguMzI4LDE3LjAxMywxOCwxNS4zMTEsMTgsMTMuNTY5VjZjMC0zLjMwOSwyLjY5MS02LDYtNnM2LDIuNjkxLDYsNnY3LjU2OWMwLDEuNzQxLTAuMzI4LDMuNDQzLTAuOTc1LDUuMDU5ICAgICBjLTAuMTIzLDAuMzA5LTAuMDg1LDAuNjU3LDAuMTAxLDAuOTMyUzI5LjYyMiwyMCwyOS45NTQsMjBINDBjMi4yMDYsMCw0LDEuNzk0LDQsNFYzMnogTTYsMzBoMzZ2LTZjMC0xLjEwMy0wLjg5Ny0yLTItMkgyOS45NTQgICAgIGMtMC45OTcsMC0xLjkyNi0wLjQ5My0yLjQ4NC0xLjMxOHMtMC42NzEtMS44NzEtMC4zMDEtMi43OTZDMjcuNzIsMTYuNTA3LDI4LDE1LjA1NSwyOCwxMy41NjlWNmMwLTIuMjA2LTEuNzk0LTQtNC00ICAgICBzLTQsMS43OTQtNCw0djcuNTY5YzAsMS40ODUsMC4yOCwyLjkzOCwwLjgzMSw0LjMxNmMwLjM3MSwwLjkyNSwwLjI1OCwxLjk3MS0wLjMwMSwyLjc5NlMxOS4wNDMsMjIsMTguMDQ2LDIySDggICAgIGMtMS4xMDMsMC0yLDAuODk3LTIsMlYzMHoiLz48L2c+PGc+PHBhdGggZD0iTTQxLDQ4SDE2Yy0wLjI2NSwwLTAuNTItMC4xMDUtMC43MDctMC4yOTNTMTUsNDcuMjY1LDE1LDQ3di0zLjc2NGwtMi4xMDUsNC4yMTFDMTIuNzI1LDQ3Ljc4NiwxMi4zNzgsNDgsMTIsNDhINyAgICAgYy0wLjU1MiwwLTEtMC40NDgtMS0xVjMxYzAtMC41NTIsMC40NDgtMSwxLTFoMzRjMC41NTIsMCwxLDAuNDQ4LDEsMXYxNkM0Miw0Ny41NTIsNDEuNTUyLDQ4LDQxLDQ4eiBNMTcsNDZoMjNWMzJIOHYxNGgzLjM4MSAgICAgbDMuNzI0LTcuNDQ3YzAuMjA4LTAuNDE1LDAuNjc0LTAuNjMxLDEuMTI1LTAuNTI2QzE2LjY4MSwzOC4xMzMsMTcsMzguNTM2LDE3LDM5TDE3LDQ2eiIvPjwvZz48Zz48cGF0aCBkPSJNMzcsNDhjLTAuNTUyLDAtMS0wLjQ0OC0xLTFWMzdjMC0wLjU1MiwwLjQ0OC0xLDEtMXMxLDAuNDQ4LDEsMXYxMEMzOCw0Ny41NTIsMzcuNTUyLDQ4LDM3LDQ4eiIvPjwvZz48Zz48cGF0aCBkPSJNMzMsNDhjLTAuNTUyLDAtMS0wLjQ0OC0xLTF2LThjMC0wLjU1MiwwLjQ0OC0xLDEtMXMxLDAuNDQ4LDEsMXY4QzM0LDQ3LjU1MiwzMy41NTIsNDgsMzMsNDh6Ii8+PC9nPjxnPjxwYXRoIGQ9Ik0yOSw0OGMtMC41NTIsMC0xLTAuNDQ4LTEtMXYtNWMwLTAuNTUyLDAuNDQ4LTEsMS0xczEsMC40NDgsMSwxdjVDMzAsNDcuNTUyLDI5LjU1Miw0OCwyOSw0OHoiLz48L2c+PGc+PGNpcmNsZSBjeD0iMjQiIGN5PSI2IiByPSIyIi8+PC9nPjwvZz48L2c+PC9zdmc+" />' .
            'Tracy clean' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $application = $this->container->getByType(Application::class);    // load system application
        $presenter = $application->getPresenter();

        // catch trait problem
        try {
            $this->parameters['InternalTracyClean'] = $presenter->link('InternalTracyClean!');
        } catch (Exception $e) {
        }

        // catch latte problem
        try {
            $latte = new Engine;
            $latte->addProvider('uiControl', $presenter);
            UIMacros::install($latte->getCompiler()); // n:href, {link }
            return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $this->parameters);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
