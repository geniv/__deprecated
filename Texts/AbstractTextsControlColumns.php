<?php

/**
 * Class AbstractTextsControlColumns, pro sloupcovy pristup (spatny)
 *
 * @author  geniv
 * @package NetteWeb
 *
 * @deprecated
 */
abstract class AbstractTextsControlColumns extends AbstractTextsControl
{
    protected $tableTexts, $database, $cache, $texts, $langCode, $extraColumns = null;


    /**
     * nacitani nebo vytvareni textu s kontrolou na ident
     * @param null $ident
     */
    protected function loadText($ident = null)
    {
        $texts = $this->cache->load('texts' . $this->langCode);
        if ($texts === null) {
            $texts = $this->getAllText();   // nacteni textu pro dany jazyk
            // ulozeni cache
            $this->cache->save('texts' . $this->langCode, $texts, [
                Nette\Caching\Cache::EXPIRE => '30 minutes',
                Nette\Caching\Cache::TAGS => ['loadText'],
            ]);
        }

        // osetreni neexistujicich nebo novych klicu
        if ($ident && !isset($texts[$ident])) {
            if ($this->isText($ident)) {
                // pokud je v databazi, nacte ho
                $texts[$ident] = $this->getText($ident);
            } else {
                // pokud neni v databazi, vytvori a nacte ho
                $this->addText($ident);
                $texts[$ident] = $this->getText($ident);
            }

            // vynuceni precachovani
            $this->cache->clean([
                Nette\Caching\Cache::TAGS => ['loadText'],
            ]);
        }
        $this->texts = $texts;
    }


    /**
     * pridani textu pro dany ident
     * @param $ident
     * @return Dibi\Result|int|mixed|null
     */
    private function addText($ident)
    {
        try {
            $lang = $this->langCode;
            $values = [
                'Ident' => $ident,
                'Language' => $lang,
            ];

            // pokud je v predkladech jako textova stranka s ## na zacatku, tak se implicitni neulozi do prekladu
            if ($this->extraColumns) {
                $extra = array_map(function ($r) use ($ident, $lang) {
                    return '## ' . $r . ' ## - ' . $ident . ' - ' . $lang;
                }, array_combine($this->extraColumns, $this->extraColumns));
                $values = array_merge($values, $extra);
            }
            return $this->database->insert($this->tableTexts, $values)
                ->execute(Dibi::IDENTIFIER);
        } catch (\Dibi\Exception $e) {
            return -$e->getCode();
        }
    }


    /**
     * existuje text pro dany ident
     * @param $ident
     * @return mixed
     */
    private function isText($ident)
    {
        return $this->database->select('Id')
            ->from($this->tableTexts)
            ->where('Language=?', $this->langCode)
            ->where('Ident=?', $ident)
            ->fetchSingle();
    }


    /**
     * nacteni textu pro dany ident
     * @param $ident
     * @return Dibi\Row|FALSE
     */
    private function getText($ident)
    {
        return $this->database->select('Id, Language' . ($this->extraColumns ? ', ' . implode(', ', $this->extraColumns) : null))
            ->from($this->tableTexts)
            ->where('Language=?', $this->langCode)
            ->where('Ident=?', $ident)
            ->fetch();
    }


    /**
     * nacteni vsech textu pro dany jazyk
     * @return array
     */
    private function getAllText()
    {
        return $this->database->select('Id, Language, Ident' . ($this->extraColumns ? ', ' . implode(', ', $this->extraColumns) : null))
            ->from($this->tableTexts)
            ->where('Language=?', $this->langCode)
            ->fetchAssoc('Ident');
    }
}
