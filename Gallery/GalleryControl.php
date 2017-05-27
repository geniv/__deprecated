<?php
use Nette\Localization\ITranslator;

/**
 * Class GalleryControl
 * pro galerii
 *
 * @author  geniv
 * @package NetteWeb
 */
class GalleryControl extends Nette\Application\UI\Control
{
    private $galleryModel, $translator;


    /**
     * GalleryControl constructor.
     * @param \App\Model\Gallery $galleryModel
     * @param ITranslator $translator
     */
    public function __construct(\App\Model\Gallery $galleryModel, ITranslator $translator=null)
    {
        parent::__construct();
        $this->galleryModel = $galleryModel;
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
        $template->setFile(__DIR__ . '/gallery.latte');
        $template->gallery = $this->galleryModel->getListGallery();
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
        $template->setFile(__DIR__ . '/galleryHomepage.latte');
        $list = $this->galleryModel->getListHomepageGallery();
        if ($limit) {
            $list->limit($limit);
        }
        $template->gallery = $list;
        $template->render();
    }
}
