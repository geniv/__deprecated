<?php

/**
 * Class AbstractTextsControl
 *
 * @author  geniv
 * @package NetteWeb
 */
abstract class AbstractTextsControl extends Nette\Application\UI\Control// implements IInvalidateCache
{
    protected $tableTexts, $database, $cache, $texts, $idLanguage, $extraColumns = null;
    private $autoCreate = false;


    /**
     * AbstractTextsControl constructor.
     * @param $tableTexts
     * @param \Dibi\Connection $database
     * @param AbstractLanguageService $language
     * @param \Nette\Caching\IStorage $cacheStorage
     */
    public function __construct($tableTexts, \Dibi\Connection $database, \AbstractLanguageService $language, \Nette\Caching\IStorage $cacheStorage)
    {
        parent::__construct();
        $this->tableTexts = $tableTexts;
        $this->database = $database;
        $this->cache = new \Nette\Caching\Cache($cacheStorage, 'cache' . get_class($this));
        $this->idLanguage = $language->getId();
    }


    /**
     * ovladani automatickeho vytvareni textovych stranek
     * @param $status
     * @return $this
     */
    public function setAutoCreate($status)
    {
        $this->autoCreate = $status;
        return $this;
    }


    /**
     * hook pro invalidaci cache
     */
//    public function hookInvalidateCache()
//    {
//        // vynuceni precachovani
//        $this->cache->clean([
//            Nette\Caching\Cache::TAGS => ['loadText'],
//        ]);
//    }


    /**
     * nastaveni extra sloupcu
     * @param $extraColumns
     * @return $this
     */
    public function setExtraColumns(array $extraColumns)
    {
        $this->extraColumns = $extraColumns;
        return $this;
    }


    /**
     * volani uzivatelsky definovanych extra sloupcu
     * @param $name
     * @param $args
     * @return mixed|void
     * @throws Exception
     */
    public function __call($name, $args)
    {
        if (!in_array($name, ['onAnchor'])) {   // nesmi zachytavat definovane metody
            $method = substr($name, 6); // nacteni jmena
            if (!isset($args[0])) {
                throw new \Exception('Nebyl zadany parametr identu.');
            }
            $ident = $args[0];  // nacteni identu
            $return = (isset($args[1]) ? $args[1] : false);

            $this->loadText($ident);
            if (isset($this->texts[$ident]) && !key_exists($method, (array)$this->texts[$ident])) {
                throw new \Exception('Nebyl nalezeny platny nazev sloupce. Sloupec ' . $method . ' neexistuje.');
            }
            // pokud existuje zaznam
            if (isset($this->texts[$ident])) {
                if ($return) {
                    return $this->texts[$ident][$method];
                }
                echo $this->texts[$ident][$method];
            } else {
                throw new \Exception('Nebyl nalezeny pozadovany zaznam pro index: ' . $ident);
            }
        }
    }


    /**
     * nacitani nebo vytvareni textu s kontrolou na ident
     * @param null $ident
     */
    protected function loadText($ident = null)
    {
        $texts = $this->cache->load('texts' . $this->idLanguage);
        if ($texts === null) {
            $texts = $this->getAllText();   // nacteni textu pro dany jazyk
            // ulozeni cache
            $this->cache->save('texts' . $this->idLanguage, $texts, [
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
                // pokud je automaticke vytvareni zapnute
                if ($this->autoCreate) {
                    // pokud neni v databazi, vytvori a nacte ho
                    $this->addText($ident);
                    $texts[$ident] = $this->getText($ident);
                }
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
            $idLanguage = $this->idLanguage;
            $values = [
                'Ident' => $ident,
                'IdLanguage' => $idLanguage,
            ];

            // pokud je v predkladech jako textova stranka s ## na zacatku, tak se implicitni neulozi do prekladu
            if ($this->extraColumns) {
                $extra = array_map(function ($r) use ($ident, $idLanguage) {
                    return '## ' . $r . ' ## - ' . $ident . ' - ' . $idLanguage;
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
            ->where('IdLanguage=?', $this->idLanguage)
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
        return $this->database->select('Id, IdLanguage' . ($this->extraColumns ? ', ' . implode(', ', $this->extraColumns) : null))
            ->from($this->tableTexts)
            ->where('IdLanguage=?', $this->idLanguage)
            ->where('Ident=?', $ident)
            ->fetch();
    }


    /**
     * nacteni vsech textu pro dany jazyk
     * @return array
     */
    private function getAllText()
    {
        return $this->database->select('Id, IdLanguage, Ident' . ($this->extraColumns ? ', ' . implode(', ', $this->extraColumns) : null))
            ->from($this->tableTexts)
            ->where('IdLanguage=?', $this->idLanguage)
            ->fetchAssoc('Ident');
    }
}
