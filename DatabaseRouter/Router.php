<?php

namespace DatabaseRouter;

use DatabaseRouter\Model;
use Exception;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\SmartObject;


/**
 * Class DatabaseRouter
 *
 * @author  geniv
 * @package NetteWeb
 */
class Router implements IRouter
{
    use SmartObject;

    private $metadata, $flags,
        $mask, $saveArray;
    private $configure = null;
    /** @var Model */
    private $routerModel = null;
    private $mainLanguage, $languages = [];

    // oddelovac strankovani
    private $vpSeparator = '_'; // implicitne: '_'
    // promenna strankovani
    private $vpVariable = 'visualPaginator-page';


    /**
     * Router constructor.
     *
     * @param Container $context
     * @param array     $metadata
     * @param int       $flags
     * @throws \Exception
     */
    public function __construct(Container $context, $metadata = [], $flags = 0)
    {
        // vnitrni struktura aliasu a ukladani parametru
        $this->mask = ['<lang>', '/', '<slug>', $this->vpSeparator, '<vp>'];  // maska pro slozeni url adresy

        $this->metadata = $metadata;
        $this->flags = $flags;

        if (isset($context->parameters['router'])) {
            // nacitani konfigurace z neonu
            $this->configure = $context->parameters['router'];
        } else {
            // implicitni nastaveni pri absenci nastaveni
            $this->configure = [
                'languageDomainSwitch' => false,
                'languageDomainAlias'  => [],
            ];
        }

        // kontrola existence modelu
        if ($context->findByType(Model::class)) {
            $this->routerModel = $context->getByType(Model::class);   // nacteni instance modelu
//dump($this->routerModel);
//            $this->routerModel->setDatabaseRouter($this);
//FIXME je tu ten blok zapotrebi?! btw prepsat router do nove podoby
//            $languageService = $this->routerModel->getLanguageService();
//            $this->mainLanguage = $languageService->getMainLanguage();
//            $this->languages = array_flip($languageService->getIdLanguages());
        } else {
            throw new Exception("Service typu " . Model::class . " neni definovana!!");
        }
    }


//TODO napsat lip komentar na filter params. + vyresit https

    public function setSecured($bool)
    {
    }


    /**
     * sestaveni url adresy podle $mask
     *
     * @param $slug
     * @param $params
     * @return string
     */
    private function buildUrl($slug, $params)
    {
        $params['slug'] = $slug;
        // vymazani jazyka pokud je stejny jako hlavni jazyk
        if (isset($params['lang']) && $params['lang'] == $this->mainLanguage) {
            $params['lang'] = '';
        }

        // host - vyhozeni jazyku z adresy
        if ($this->configure['languageDomainSwitch']) {
            unset($params['lang']);
        }

        // transformace strankovani do vnitrni promenne
        if (isset($params[$this->vpVariable])) {
            $params['vp'] = $params[$this->vpVariable];
        }

        // doplneni pole parametru do masky
        $adrArray = array_map(function ($r) use ($params) {
            if (preg_match('/\<([a-z]+)\>/', $r, $m) && $m && isset($m[1])) {
                return (isset($params[$m[1]]) ? $params[$m[1]] : null);
            } else {
                return $r;
            }
        }, $this->mask);

        return trim(implode($adrArray), '/_');  // slozeni pole a ocesani lemited a podtrzitek
    }


    /**
     * Maps HTTP request to a Request object.
     *
     * @param IRequest $httpRequest
     * @return Request|null
     */
    public function match(IRequest $httpRequest)
    {
        $alias = $httpRequest->getUrl()->getPathInfo();
        $presenter = null;
        $parameters = [];

        // vlozeni defaultnich hodnot pri prazdne adrese
        if (!$alias && $this->metadata) {
            $parameters = $this->metadata;  // vlozeni parametru z metadat
            // pokud je definovany presenter tak ho preda dal a promaze index
            if (isset($parameters['presenter'])) {
                $presenter = $parameters['presenter'];
                unset($parameters['presenter']);
            }
        }

        $lang = null;
        // url nastavovani jazyka rovnou z adresy pro spravnou funkci systemu (db + preklady)
        if (preg_match('/((?<lang>[a-z]{2})\/)?/', $alias, $m) && isset($m['lang'])) {
            $lang = $m['lang'];
            $alias = trim(substr($alias, strlen($m['lang']), strlen($alias)), '/_');   // ocesani jazyka od slugu
        }

        // host detekce - rozlisovani podle domeny
        if ($this->configure['languageDomainSwitch']) {
            $host = $httpRequest->url->host;    // nacteni url hostu pro zvoleni jazyka
            if (isset($this->configure['languageDomainAlias'][$host])) {
                // v pripade ze existuje domenovy alias
                $parameters['lang'] = $this->configure['languageDomainAlias'][$host];
            } else {
                // pokud nenajde domenovy alias
                $parameters['lang'] = $this->mainLanguage;
            }
        }

        // nastavovani jazyku pro jazykovou sluzbu
        if (isset($parameters['lang'])) {
            $lang = $parameters['lang'];
        }
//        $this->routerModel->getLanguageService()->setCodeLanguage($lang);   // nastaveni jazykove sluzby

        // uprava slugu kvuli strankovani
        if (preg_match('/(.+\\' . $this->vpSeparator . '(?<vp>[0-9]+))?/', $alias, $m) && isset($m['vp'])) {
            $parameters[$this->vpVariable] = $m['vp'];
            $alias = trim(substr($alias, 0, strlen($alias) - strlen($m['vp'])), '/_'); // ocesani strankovani od slugu
        }

        // akceptace adresy kde je na konci zbytecne lomitko, odebere posledni lomitko
        $alias = rtrim($alias, '/_');


        // pokud je definovany alias
        if ($alias) {
            $c = $this->routerModel->getByAlias($lang, $alias);  // nacteni routy podle slugu
            if ($c) {
                // nacreni presenteru z databaze
                $presenter = $c->presenter;

                // nacteni extra parametru z databaze
//                if ($c->Parameters) {
//                    $parameters += unserialize($c->Parameters);
//                }

                // nacteni jazykz z databaze
                $parameters['lang'] = $this->languages[$c->id_language];

                // nacteni akce z databaze
                $parameters['action'] = $c->action;

                // nacteni id z databaze
                if ($c->id_item) {
                    $parameters['id'] = $c->id_item;
                }
            } else {
                return null;
            }
        }

        // pridani query parametru
        $parameters += $httpRequest->getQuery();

        // ochrana proti prazdnemu presenteru
        if (!$presenter) {
            return null;
        }

        return new Request(
            $presenter,
            $httpRequest->getMethod(),
            $parameters,
            $httpRequest->getPost(),
            $httpRequest->getFiles(),
            [Request::SECURED => $httpRequest->isSecured()]
        );
    }


    /**
     * Constructs absolute URL from Request object.
     *
     * @param Request $appRequest
     * @param Url     $refUrl
     * @return null|string
     */
    public function constructUrl(Request $appRequest, Url $refUrl)
    {
        if ($this->flags & self::ONE_WAY) {
            return null;
        }
//TODO loader makra do DI narovinu z kdyby?!
        $c = $this->routerModel->getAliasByParams($appRequest->presenterName, $appRequest->parameters); // nacteni aliasu podle presenteru a parametru
        if ($c) {
            $alias = $this->buildUrl($c->alias, $appRequest->parameters); // slozeni adresy

            $loadParams = $appRequest->parameters;
            // vyhozeni jiz ukladanych hodnot
            unset($loadParams['lang'], $loadParams['action'], $loadParams['id'], $loadParams['vp'], $loadParams[$this->vpVariable]);

            // odstraneni extra parametru
//            if ($this->saveArray) {
//                foreach ($this->saveArray as $item) {
//                    unset($loadParams[$item]);
//                }
//            }

            // vytvoreni url adresy
            $url = new Url($refUrl->getBaseUrl() . $alias);
            $url->setScheme($this->flags & self::SECURED ? 'https' : 'http');
            $url->setQuery($loadParams);
            return $url->getAbsoluteUrl();
        } else {
            // pokud je aktivni detekce podle domeny tak preskakuje FORWARD metodu nebo Homepage presenter
            if ($this->configure['languageDomainSwitch'] && ($appRequest->method != 'FORWARD' || $appRequest->presenterName == 'Homepage')) {
                $url = new Url($refUrl->getBaseUrl());
                $url->setScheme($this->flags & self::SECURED ? 'https' : 'http');
                return $url->getAbsoluteUrl();
            }
            return null;
        }
    }
}
