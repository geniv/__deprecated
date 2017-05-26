<?php

namespace AliasRouter;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\Url;


/**
 * Class Router
 *
 * @author  geniv
 * @package AliasRouter
 */
class Router implements IRouter
{
    /** @var bool default inactive https */
    private $secure = false;
    /** @var bool default inactive one way router */
    private $oneWay = false;
    /** @var Model router model */
    private $model;
    /** @var array default parameters */
    private $defaultParameters = [];


    /**
     * Router constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->model = $container->getByType(Model::class);
    }


    /**
     * Enable https, defalt is disable.
     *
     * @param bool $secure
     * @return $this
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
        return $this;
    }


    /**
     * Enable one way router.
     *
     * @param bool $oneWay
     * @return $this
     */
    public function setOneWay($oneWay)
    {
        $this->oneWay = $oneWay;
        return $this;
    }


    /**
     * Set default parameters, presenter, action and locale.
     *
     * @param $presenter
     * @param $action
     * @param $locale
     * @return $this
     */
    public function setDefaultParameters($presenter, $action, $locale)
    {
        $this->defaultParameters = [
            'presenter' => $presenter,
            'action'    => $action,
            'locale'    => $locale,
        ];
        return $this;
    }


    /**
     * Maps HTTP request to a Request object.
     *
     * @param IRequest $httpRequest
     * @return Request|NULL
     */
    function match(IRequest $httpRequest)
    {
        $pathInfo = $httpRequest->getUrl()->getPathInfo();

        // parse locale
        $locale = $this->defaultParameters['locale'];
        if (preg_match('/((?<locale>[a-z]{2})\/)?/', $pathInfo, $m) && isset($m['locale'])) {
            $locale = trim($m['locale'], '/_');
            $pathInfo = trim(substr($pathInfo, strlen($m['locale'])), '/_');   // ocesani slugu
        }

        // parse alias
        $alias = null;
        if (preg_match('/((?<alias>[a-z0-9-\/]+)(\/)?)?/', $pathInfo, $m) && isset($m['alias'])) {
            $alias = trim($m['alias'], '/_');
            $pathInfo = trim(substr($pathInfo, strlen($m['alias'])), '/_');   // ocesani jazyka od slugu
        }

        // parse paginator
        $vp = null;
        if (preg_match('/((?<vp>[a-z0-9-]+)(\/)?)?/', $pathInfo, $m) && isset($m['vp'])) {
            $parameters['vp'] = trim($m['vp'], '/_');
        }

        // set default presenter
        $presenter = $this->defaultParameters['presenter'];

        // set locale to parameters
        $parameters['locale'] = $locale;

        // akceptace adresy kde je na konci zbytecne lomitko, odebere posledni lomitko
        $alias = rtrim($alias, '/_');

        if ($alias) {
            // load parameters from database
            $param = $this->model->getParametersByAlias($locale, $alias);
            if ($param) {
                $presenter = $param->presenter;
                $parameters['action'] = $param->action;
                if ($param->id_item) {
                    $parameters['id'] = $param->id_item;
                }
            } else {
                return null;
            }
        }

        $parameters += $httpRequest->getQuery();

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
     * @return NULL|string
     */
    function constructUrl(Request $appRequest, Url $refUrl)
    {
        if ($this->oneWay) {
            return null;
        }

        $param = $this->model->getAliasByParameters($appRequest->presenterName, $appRequest->parameters);
        if ($param) {
            $parameters = $appRequest->parameters;

            $part = implode('/', array_filter([$this->model->getCodeLocale(), $param->alias, $param->id_item]));
            $alias = trim(isset($parameters['vp']) ? implode('_', [$part, $parameters['vp']]) : $part, '/_');

            unset($parameters['locale'], $parameters['action'], $parameters['alias'], $parameters['id'], $parameters['vp']);

            // create url address
            $url = new Url($refUrl->getBaseUrl() . $alias);
            $url->setScheme($this->secure ? 'https' : 'http');
            $url->setQuery($parameters);
            return $url->getAbsoluteUrl();
        }

        //TODO domain routing!!!!

        return null;
    }
}
