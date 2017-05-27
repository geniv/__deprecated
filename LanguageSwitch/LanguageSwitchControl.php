<?php

use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

/**
 * Class LanguageSwitchControl
 *
 * @author  geniv
 * @package NetteWeb
 */
class LanguageSwitchControl extends Control
{
    private $language, $translator, $request;
    private $pathTemplate;


    /**
     * LanguageSwitchControl constructor.
     * @param BaseLanguageService $language
     * @param ITranslator $translator
     * @param \Nette\Http\Request $request
     */
    public function __construct(\BaseLanguageService $language, ITranslator $translator, Nette\Http\Request $request)
    {
        parent::__construct();
        $this->language = $language;
        $this->translator = $translator;
        $this->request = $request;

        // nastaveni implcitni cesty
        $this->pathTemplate = __DIR__ . '/LanguageSwitch.latte';
    }


    /**
     * nastavovani cesty pro template
     * @param $path
     * @return $this
     */
    public function setPathTemplate($path)
    {
        $this->pathTemplate = $path;
        return $this;
    }


    /**
     * render komponenty
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile($this->pathTemplate);

        $template->host = null;
        if ($this->presenter->context->parameters['router']['languageDomainSwitch']) {
            $template->flipLanguageDomainAlias = array_flip($this->presenter->context->parameters['router']['languageDomainAlias']);
        }

        $template->languageName = $this->language->getNameLanguages();
        $template->languages = $this->language->getNameLanguages();
        $template->languageCode = $this->language->getCodeLanguage();

        $template->render();
    }

    /**
     * @return LanguageSwitchControl
     */
    public function create($id)
    {
        dump($id);
    }
}


class LanguageSwitch{
    public function __construct($id, $hell, $aa)
    {
        dump($id, $hell, $aa);
    }
}

interface ILanguageSwitch
{

    /**
     * @param $id
     * @return LanguageSwitch
     */
    public function create($id, $hell);
}
