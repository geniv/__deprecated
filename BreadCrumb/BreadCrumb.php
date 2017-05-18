<?php

/**
 * Class BreadCrumb
 *
 * Breadcrumb Component
 * @author David Zadražil <me@davidzadrazil.cz> edit by Leonardo Allende <alnux@ya.ru> edit by Radek Frystak <geniv.radek@gmail.com>
 *
 */
class BreadCrumb extends Nette\Application\UI\Control
{
    /** @var array links */
    private $links = [];
    private $pathTemplate;


    /**
     * BreadCrumb constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // nastaveni implcitni cesty
        $this->pathTemplate = __DIR__ . '/BreadCrumb.latte';
    }


    /**
     * nastavovani cesty pro template
     * @param $path
     * @return $this
     */
    public function setPathTemplate($path)
    {
        $this->pathTemplate = $path;
        return $this;
    }


    /**
     * Render function
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setFile($this->pathTemplate);

        $template->links = $this->links;
        $template->render();
    }


    /**
     * Add link
     * @param $title PRELOZENY text!!!
     * @param null $link
     * @param null $icon
     */
    public function addLink($title, $link = null, $icon = null)
    {
        $this->links[md5($title)] = [
            'title' => $title,
            'link' => is_array($link) ? $link[0] : $link,   // moznost ukladani linky s parametry
            'linkArgv' => is_array($link) ? array_slice($link, 1) : [],   // rozsirujici parametry odkazu
            'icon' => $icon
        ];
    }


    /**
     * Remove link
     * @param $key
     * @throws Exception
     */
    public function removeLink($key)
    {
        $key = md5($key);
        if (array_key_exists($key, $this->links)) {
            unset($this->links[$key]);
        } else {
            throw new \Exception("Key does not exist.");
        }
    }


    /**
     * Edit link
     * @param $title
     * @param null $link
     * @param null $icon
     */
    public function editLink($title, $link = null, $icon = null)
    {
        if (array_key_exists(md5($title), $this->links)) {
            $this->addLink($title, $link, $icon);
        }
    }
}
