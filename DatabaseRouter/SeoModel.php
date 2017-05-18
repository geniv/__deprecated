<?php

namespace DatabaseRouter;

use DatabaseRouter\Model;
use Nette\Application\UI\Presenter;


/**
 * Class SeoModel
 *
 * @author  geniv
 * @package DatabaseRouter
 */
class SeoModel extends Model
{

    /**
     * vkladani a nacitani seo informaci
     *
     * @param Presenter $context
     * @return \Dibi\Fluent|\Dibi\Row|FALSE|null
     */
    public function getSeo(Presenter $context)
    {
        $tableRouteSeo = $this->tableRoute . '_seo';

        // nacteni promennych z kontextu
        $presenter = $context->getName();
        $action = $context->getParameter('action');
        $id = $context->getParameter('id');

        $idRoute = $this->getIdRoute($presenter, $action);  // nacteni id routeru
        if (!$idRoute) {
            $idRoute = $this->insertRoute($presenter, $action);
        }

        // zachytavani extremnich stavu
        if (!$idRoute || in_array($presenter, ['Error', 'Error4xx'])) {
            return null;
        }

        // nacteni jazyka ze sluzby
        $lang = $this->locale->getCodeLanguage();
        $idLanguage = $this->locale->getIdLanguageByCode($lang);

        $cursor = $this->database->select('id, title, description')
            ->from($tableRouteSeo)
            ->where('id_route=%i', $idRoute)
            ->where('id_language=%s', $idLanguage);
        // pokud je definovano id
        if ($id) {
            $cursor->where('id_item=%i', $id);
        } else {
            $cursor->where('id_item IS NULL');
        }

        $cursor = $cursor->fetch();
        if (!$cursor) {
            // vlozeni prazdneho zaznamu
            $values = [
                'id_route'    => $idRoute,
                'id_item'     => $id,
                'id_language' => $idLanguage,
            ];
            $this->database->insert($tableRouteSeo, $values)
                ->execute();
        } else {
            // vraceni nacteneho zaznamu
            return $cursor;
        }
        return null;
    }
}
