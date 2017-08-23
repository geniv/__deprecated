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

    /** @var Seo */
    protected $seo;


    /**
     * FilterSeo constructor.
     *
     * @param Seo $seo
     */
    public function __construct(Seo $seo)
    {
        $this->seo = $seo;
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
