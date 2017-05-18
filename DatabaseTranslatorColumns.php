<?php

/**
 * Class DatabaseTranslatorColumns
 * databazovy s podporou Pluralu, pro sloupcovy pristup (spatny)
 *
 * @author  geniv
 * @package NetteWeb
 *
 * @deprecated
 */
class DatabaseTranslatorColumns extends AbstractDatabaseTranslator implements IAbstractTranslator
{

    /**
     * interni nacitani prekladu z db
     * @return array
     */
    public function loadTranslate()
    {
        $code = $this->language->getCode(true);
        return $this->database->select('Id, Ident, ' . $code)
            ->from($this->tableTranslate)
            ->fetchPairs('Ident', $code);
    }


    /**
     * interni vkladani jednotlivych prekladu do db
     * @param $index
     * @param $message
     * @return null|string
     */
    public function saveTranslate($index, $message)
    {
        $code = $this->language->getCode();
        if ($code != $this->language->getMainLang()) {
            $message = sprintf('## %s ##', $message);   // defaultni prekladovy text
        }

        $arr = [
            'Ident' => $index,      // ukladani identifikatoru
            'Sample' => $message,   // ukladani originalniho textu
            $this->language->getCode(true) => $message, // ukladani do zkratky jazyka
        ];

        // sample uklada jen pro hlavni jazyk aplikace
        if ($code != $this->language->getMainLang()) {
            unset($arr['Sample']);
        }

        $this->database->query('INSERT INTO %n %v ON DUPLICATE KEY UPDATE %a', $this->tableTranslate, $arr, $arr);   // vlozeni/update do databaze
        $this->dictionary[$index] = $message;   // pridani slozeneho pole do slovniku
        $this->saveCache();

        // vraceni textu
        return $message;
    }
}
