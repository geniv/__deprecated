<?php

namespace Seo;

use Latte\Runtime\FilterInfo;


/**
 * Class FilterTitle
 *
 * @author  geniv
 * @package Seo
 */
class FilterTitle extends FilterSeo
{

    /**
     * Magic call from template.
     *
     * @param FilterInfo $info
     * @param            $string
     * @return string
     */
    public function __invoke(FilterInfo $info, $string)
    {
        return $this->dibiSeo->getItem($this->application, ['title' => $string]);
    }
}
