<?php

namespace App\Presenters;

/**
 * Class PagePresenter
 *
 * @author  geniv
 * @package NetteWeb
 */
class PagePresenter extends BasePresenter
{

    /**
     * starter
     */
    public function startup()
    {
        parent::startup();

        $this->template->setFile(__DIR__ . '/templates/Page/default.latte');
        // nacteni identu z atributu
        $ident = $this->action;
        // drobeckova navigace
        $this->breadcrumbs->addLink($ident);
        // identifikator stranky
        $this->template->ident = $ident;
    }
}
