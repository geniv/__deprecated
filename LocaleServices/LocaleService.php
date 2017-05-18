<?php

namespace __LocaleServices;

use Nette\Application\Application;
use Nette\Application\Request;
use Nette\SmartObject;


/**
 * Class LanguageService
 *
 * @author  geniv
 * @package LanguageServices
 */
class LocaleService
{
    use SmartObject;

    /** @var array pole jazyku */
    private $locales;
    /** @var array jazykove aliasy */
    private $aliasLocale;
    /** @var string vychozi jazyk */
    private $defaultLocale;
    /** @var string vybrany jazyk */
    private $selectLocale;


    /**
     * LocaleService constructor.
     *
     * @param      $defaultLocale
     * @param      $locales
     * @param null $localeAlias
     */
    protected function __construct($defaultLocale, $locales, $localeAlias = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
        $this->aliasLocale = $localeAlias;

        // implicitni nastaveni vyraneho jakyka
        $this->selectLocale = $this->defaultLocale;
    }


    /**
     * nacitani jazyku z http requestu
     *
     * @param Application $sender
     * @param Request     $request
     */
    public function onRequest(Application $sender, Request $request)
    {
        $params = $request->getParameters();
        $this->setCode($params['locale']);
    }


    /**
     * nacteni seznamu jmen jazyku
     *
     * @return array
     */
    public function getListName()
    {
        if ($this->locales) {
            return array_map(function ($item) {
                return $item['name'];
            }, $this->locales);
        }
        return [];
    }


    /**
     * nacteno seznamu id jazyku
     *
     * @return array
     */
    public function getListId()
    {
        if ($this->locales) {
            return array_map(function ($item) {
                return $item['id'];
            }, $this->locales);
        }
        return [];
    }


    /**
     * nacteni pole jazyku
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }


    /**
     * interni kontrola vybraneho jazyka a aplikace aliasu
     */
    private function checkLanguage()
    {
        if (!isset($this->locales[$this->selectLocale])) {
            // pokud neexistuje jazky hleda v aliasech
            if (isset($this->aliasLocale[$this->selectLocale])) {
                $this->selectLocale = $this->aliasLocale[$this->selectLocale];
            } else {
                $this->selectLocale = $this->defaultLocale;
            }
        }
    }


    /**
     * nacteni aktualniho jazyka
     *
     * @param bool $upper
     * @return mixed
     */
    public function getCode($upper = false)
    {
        $this->checkLanguage();
        $code = $this->locales[$this->selectLocale]['code'];
        return ($upper ? strtoupper($code) : $code);
    }


    /**
     * nastaveni aktualniho jazyka
     *
     * @param $code
     */
    public function setCode($code)
    {
        if ($code) {
            $this->selectLocale = strtolower($code);
            $this->checkLanguage();
        }
    }


    /**
     * nacteni id aktualniho jazyka
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->locales[$this->selectLocale]['id'];
    }


    /**
     * nacteni id hlavniho jazyka
     *
     * @return null
     */
    public function getIdDefault()
    {
        return $this->locales[$this->defaultLocale]['id'];
    }


    /**
     * nacte kod hlavniho jazyku
     *
     * @param bool $upper
     * @return string
     */
    public function getCodeDefault($upper = false)
    {
        return ($upper ? strtoupper($this->defaultLocale) : $this->defaultLocale);
    }


    /**
     * nacteni pluralu jazyka
     *
     * @return mixed
     */
    public function getPlural()
    {
        return $this->locales[$this->selectLocale]['plural'];
    }


    /**
     * nacita id jazyka podle kodu
     *
     * @param $code
     * @return mixed
     */
    public function getIdByCode($code)
    {
        // pokud existuje kod v
        if (isset($this->locales[$code]['id'])) {
            return $this->locales[$code]['id'];
        }
        // pokud existuje kod v aliasu
        if (isset($this->aliasLocale[$code])) {
            $this->locales[$this->aliasLocale[$code]]['id'];
        }
        // jinak pouzije defaultni jazyk
        return $this->locales[$this->defaultLocale]['id'];
    }
}
