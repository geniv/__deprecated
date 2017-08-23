<?php

namespace Seo;

use Latte\Runtime\FilterInfo;
use Nette\Application\Application;
use Nette\SmartObject;


/**
 * Class FilterSeo
 *
 * @author  geniv
 * @package Seo
 */
abstract class FilterSeo
{
    use SmartObject;

    /** @var DibiSeo */
    protected $dibiSeo;
    /** @var Application */
    protected $application;


    /**
     * FilterSeo constructor.
     *
     * @param DibiSeo     $dibiSeo
     * @param Application $application
     */
    public function __construct(DibiSeo $dibiSeo, Application $application)
    {
        $this->dibiSeo = $dibiSeo;
        $this->application = $application;
    }


    /**
     * Magic call from template.
     *
     * @param FilterInfo $info
     * @param            $string
     * @return string
     */
    abstract public function __invoke(FilterInfo $info, $string);
}
