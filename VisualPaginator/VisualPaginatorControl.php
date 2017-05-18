<?php

/**
 * Class VisualPaginatorControl
 *
 * @author  geniv
 * @package NetteWeb
 */
class VisualPaginatorControl extends Nette\Application\UI\Control
{
    private $translator, $paginator, $steps, $pathTemplate;


    /**
     * VisualPaginatorControl constructor.
     * @param AbstractTranslator $translator
     */
    public function __construct(\AbstractTranslator $translator)
    {
        parent::__construct();
        $this->translator = $translator;
        // vytvoreni paginatoru
        $this->paginator = new Nette\Utils\Paginator;
        // pole kroku pro strankovani
        $this->steps = [];
        // implicitni cesta k template
        $this->pathTemplate = __DIR__ . '/VisualPaginator.latte';
    }


    /**
     * Returns current page number.
     * @return int
     */
    public function getPage()
    {
        return $this->paginator->getPage();
    }


    /**
     * Sets current page number.
     * @param $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->paginator->setPage($page);
        return $this;
    }


    /**
     * Sets the number of items to display on a single page.
     * @param $itemsPerPage
     * @return $this
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->paginator->setItemsPerPage($itemsPerPage);
        return $this;
    }


    /**
     * Sets the total number of items.
     * @param $itemCount
     * @return $this
     */
    public function setItemCount($itemCount)
    {
        $this->paginator->setItemCount($itemCount);
        return $this;
    }


    /**
     * Returns the absolute index of the first item on current page.
     * @return int
     */
    public function getOffset()
    {
        return $this->paginator->getOffset();
    }


    /**
     * Returns the absolute index of the first item on current page in countdown paging.
     * @return mixed
     */
    public function getCountdownOffset()
    {
        return $this->paginator->getCountdownOffset();
    }


    /**
     * Returns the number of items on current page.
     * @return mixed
     */
    public function getLength()
    {
        return $this->paginator->getLength();
    }


    /**
     * nastaveni cesty template
     * @param $path
     * @return $this
     */
    public function setPathTemplate($path)
    {
        $this->pathTemplate = $path;
        return $this;
    }


    /**
     * interni render
     */
    private function internalRender()
    {
        $template = $this->getTemplate();

        $template->paginator = $this->paginator;
        $template->steps = $this->steps;

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile($this->pathTemplate);
        $template->render();
    }


    /**
     * nastaveni implicitnich kroku
     */
    private function implicitSteps()
    {
        if ($this->paginator->pageCount > 0) {
            $this->steps = range(1, $this->paginator->pageCount);
        }
    }


    /**
     * defaultni render
     * render type1
     */
    public function render()
    {
        $this->renderType1();
    }


    /**
     * render typu 1
     * plne zobrazeni - () 1 2 3 4 (plne) ()
     */
    public function renderType1()
    {
        $this->implicitSteps();
        $this->internalRender();
    }


    /**
     * render typu 2
     * zkracene zobrazovani s prvnim - () 1 ... 4 5 6 ... 9 ()
     * minimum: 6 stranek, do 6 type 1
     * @param int $koef
     * @param int $range
     */
    public function renderType2($koef = 4, $range = 1)
    {
        $page = $this->paginator->page;
        $last = $this->paginator->pageCount;

        if ($this->paginator->pageCount >= 6) {
            $steps = [$this->paginator->base];
            if ($page < $koef) {
                $steps = array_merge($steps, range(2, $koef), [NULL]);
            } else if ($page >= $koef && $page <= $last - ($koef - 1)) {
                $steps = array_merge($steps, [NULL], range($page - $range, $page + $range), [NULL]);
            } else if ($page > $last - ($koef - 1)) {
                $steps = array_merge($steps, [NULL], range($last - ($koef - 1), $last - 1));
            }
            $steps[] = $this->paginator->pageCount;

            $this->steps = $steps;
        } else {
            $this->implicitSteps();
        }
        $this->internalRender();
    }


    /**
     * render typu 3
     * zkracene zobrazovani - () 4 5 6 ()
     * minimum: 4 stranky, do 4 type 1
     * @param int $koef
     * @param int $range
     */
    public function renderType3($koef = 4, $range = 1)
    {
        $page = $this->paginator->page;
        $last = $this->paginator->pageCount;

        if ($this->paginator->pageCount >= 4) {
            if ($page < $koef) {
                $steps = range(1, $koef);
            } else if ($page >= $koef && $page <= $last - ($koef - 1)) {
                $steps = range($page - $range, $page + $range);
            } else if ($page > $last - ($koef - 1)) {
                $steps = range($last - ($koef - 1), $last);
            }

            $this->steps = $steps;
        } else {
            $this->implicitSteps();
        }
        $this->internalRender();
    }
}
