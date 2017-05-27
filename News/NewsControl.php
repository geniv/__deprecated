<?php

use Nette\Localization\ITranslator;

/**
 * Class NewsControl
 * pro novinky
 *
 * @author  geniv
 * @package NetteWeb
 */
class NewsControl extends Nette\Application\UI\Control
{
    private $newsModel, $translator;


    /**
     * NewsControl constructor.
     * @param \App\Model\News $newsModel
     */
    public function __construct(\App\Model\News $newsModel, ITranslator $translator = null)
    {
        parent::__construct();
        $this->newsModel = $newsModel;
        $this->translator = $translator;
    }


    /**
     * defaultni render
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile(__DIR__ . '/news.latte');
        $template->news = $this->newsModel->getListNews();
        $template->render();
    }


    /**
     * homepage render
     * @param null $limit
     */
    public function renderHomepage($limit = null)
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile(__DIR__ . '/newsHomepage.latte');
        $list = $this->newsModel->getListHomepageNews();
        if ($limit) {
            $list->limit($limit);
        }
        $template->news = $list;
        $template->render();
    }
}
