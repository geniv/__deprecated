<?php

namespace LocaleServices\Bridges\Tracy;

use Latte\Engine;
use LocaleServices\LocaleService;
use Nette\DI\Container;
use Nette\SmartObject;
use Tracy\Debugger;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package LocaleServices\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    private $localeService;
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
     * @param LocaleService $localeService
     */
    public function register(LocaleService $localeService)
    {
        $this->localeService = $localeService;
        Debugger::getBar()->addPanel($this);
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return '<span title="Locale"><img width="16px" height="16px" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDI0IDI0IiBoZWlnaHQ9IjI0cHgiIGlkPSJMYXllcl8xIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjI0cHgiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxnPjxwYXRoIGQ9Ik0xMiwwQzYuNSwwLDIsNC41LDIsMTBzMyw3LDEwLDE0YzctNywxMC04LjUsMTAtMTRTMTcuNSwwLDEyLDB6IE0xMiwyMS41Yy01LTUtOC03LjEtOC0xMS41czMuNi04LDgtOHM4LDMuNiw4LDggICBTMTcsMTYuNSwxMiwyMS41eiIvPjxwYXRoIGQ9Ik0xMiw2Yy0yLjIsMC00LDEuOC00LDRzMS44LDQsNCw0czQtMS44LDQtNFMxNC4yLDYsMTIsNnogTTEyLDEzYy0xLjcsMC0zLTEuMy0zLTNzMS4zLTMsMy0zczMsMS4zLDMsM1MxMy43LDEzLDEyLDEzeiIvPjwvZz48L3N2Zz4=" />' .
            'Locale - <strong>' . ($this->localeService->getCode() ?: 'NULL') . '</strong>' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $params = [
            'locales'         => $this->localeService->getLocales(),
            'localeClass'     => get_class($this->localeService),
            'localeIdDefault' => $this->localeService->getIdDefault(),
            'localeId'        => $this->localeService->getId(),
        ];

        $latte = new Engine;
        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);
    }
}
