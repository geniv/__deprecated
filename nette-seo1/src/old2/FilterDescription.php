<?php

namespace Seo;

use Latte\Runtime\FilterInfo;


/**
 * Class FilterDescription
 *
 * @author  geniv
 * @package Seo
 */
class FilterDescription extends FilterSeo
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
        return $this->dibiSeo->getItem($this->application, ['description' => $string]);
    }
}
