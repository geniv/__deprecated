<?php

namespace __AliasRouter;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\Url;


/**
 * Class Router
 *
 * @package AliasRouter
 */
class __Router implements IRouter
{
    /** @var string implicit url separator */
    private $urlSeparator = '/_';
    /** @var bool implicit deactivate https */
    private $secure = false;
    /** @var null input pattern */
    private $inputPattern = null;
    /** @var Model router model */
    private $model;

    private $implicitParametrs = [];
    private $matchData = [];
    private $matchIndex = -1;


    /**
     * Router constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->model = $container->getByType(Model::class);

//        , $inputPattern
//        $this->inputPattern = $inputPattern;
    }


    /**
     * set another url separator
     *
     * @param string $urlSeparator
     * @return $this
     */
    public function setUrlSeparator($urlSeparator)
    {
        if ($urlSeparator) {
            $this->urlSeparator = $urlSeparator;
        }
        return $this;
    }


    /**
     * enable https, implicit disable
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
     * load keys parameters
     *
     * @return array
     */
//    private function getKeys()
//    {
//        $splitPattern = '/\\' . implode('|\\', str_split($this->urlSeparator)) . '/';
//        return preg_split($splitPattern, $this->inputPattern);
//    }


    /**
     * set implicit values for presenter and action
     *
     * @param $presenter
     * @param $action
     * @return $this
     */
    public function setDefaultPresenterAction($presenter, $action)
    {
        $this->implicitParametrs = [
            'presenter' => $presenter,
            'action'    => $action,
        ];
        return $this;
    }


    /**
     * add router match
     *
     * @param      $variable
     * @param null $defaultValue
     * @return $this
     */
    public function addMatch($variable, $defaultValue = null)
    {
        $this->matchIndex++;
        $this->matchData[$this->matchIndex] = [
            '_name'        => 'name' . uniqid(),
            'variable'     => $variable,
            'defaultValue' => $defaultValue,
            'pattern'      => '(?<%name%>[a-z0-9-]+)(\/)?',
            'separator'    => '/',
        ];
        return $this;
    }


    /**
     * set next url separator
     *
     * @param $separator
     * @return $this
     */
    public function setNextSeparator($separator)
    {
        $this->matchData[$this->matchIndex]['separator'] = $separator;
        return $this;
    }


    /**
     * set router pattern
     *
     * @param $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->matchData[$this->matchIndex]['pattern'] = $pattern;
        return $this;
    }


    /**
     * add predefined router locale match
     *
     * @return $this
     */
    public function setLocale($localeCodeDefault)
    {
        $this->addMatch('locale', $localeCodeDefault)->setPattern('(?<%name%>[a-z]{2})\/');
        return $this;
    }


    /**
     * predefined alias router
     *
     * @return $this
     */
    public function setAlias()
    {
        $this->addMatch('alias')->setNextSeparator('_');
        $this->addMatch('visualPaginator-page')->setPattern('(?<%name%>[0-9]+)');
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
        $alias = $httpRequest->getUrl()->getPathInfo();

//        dump($this->matchData);

        $parameters = [];
        foreach ($this->matchData as $data) {
            $pattern = str_replace(['%name%'], [$data['_name']], $data['pattern']);
            if (preg_match('/(' . $pattern . ')?/', $alias, $m)) {
                if (isset($m[$data['_name']])) {
                    $parameters[$data['variable']] = trim($m[$data['_name']], $data['separator']);
//                    dump(substr($alias, strlen($m[$data['_name']])));
                    $alias = trim(substr($alias, strlen($m[$data['_name']])), $data['separator']);

                } else {
                    $parameters[$data['variable']] = $data['defaultValue'];
                }
            }
        }

        dump($parameters);


//        if (preg_match('/((?<lang>[a-z0-9-]+)(\/)?)?/', $alias, $m) && isset($m['lang'])) {
//            dump($m['lang']);
//            $alias = trim(substr($alias, strlen($m['lang'])), '/_');   // ocesani jazyka od slugu
//        }
//
//        if (preg_match('/((?<sk1>[a-z0-9-]+)(\/)?)?/', $alias, $m) && isset($m['sk1'])) {
//            dump($m['sk1']);
//            $alias = trim(substr($alias, strlen($m['sk1'])), '/_');   // ocesani jazyka od slugu
//        }
//
//        if (preg_match('/((?<sk2>[a-z0-9-]+)(\/)?)?/', $alias, $m) && isset($m['sk2'])) {
//            dump($m['sk2']);
//            $alias = trim(substr($alias, strlen($m['sk2'])), '/_');   // ocesani jazyka od slugu
//        }
//
//        if (preg_match('/((?<alias>[a-z0-9-]+)(\/)?)?/', $alias, $m) && isset($m['alias'])) {
//            dump($m['alias']);
//            $alias = trim(substr($alias, strlen($m['alias'])), '/_');   // ocesani jazyka od slugu
//        }
//
//        if (preg_match('/((?<vp>[a-z0-9-]+)(\/)?)?/', $alias, $m) && isset($m['vp'])) {
//            dump($m['vp']);
//            $alias = trim(substr($alias, strlen($m['vp'])), '/_');   // ocesani jazyka od slugu
//        }
//        dump($alias);

        dump('----------------------');

        die;

        // TODO: Implement match() method.

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
        if ($this->flags & self::ONE_WAY) {
            return null;
        }

        // TODO: Implement constructUrl() method.

        // vytvoreni url adresy
        $url = new Url($refUrl->getBaseUrl() . $alias);
        $url->setScheme($this->flags & Request::SECURED ? 'https' : 'http');
        $url->setQuery($loadParams);
        return $url->getAbsoluteUrl();
    }
}
