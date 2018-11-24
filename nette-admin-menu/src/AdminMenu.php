<?php declare(strict_types=1);

namespace AdminMenu;

use GeneralForm\ITemplatePath;
use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;
use Nette\Localization\ITranslator;


/**
 * Class AdminMenu
 *
 * @author  geniv
 * @package AdminMenu
 */
class AdminMenu extends Control implements ITemplatePath
{
    /** @var ITranslator */
    private $translator = null;
    /** @var string */
    private $templatePath;


    /**
     * AdminMenu constructor.
     *
     * @param ITranslator|null $translator
     */
    public function __construct(ITranslator $translator = null)
    {
        parent::__construct();

        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/AdminMenu.latte'; // path
    }


    /**
     * Set template path.
     *
     * @param string $path
     */
    public function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
    }


    public function addInnerComponent(IComponent $component, $name)
    {
        $this->addComponent($component, $name);
    }


    public function setListGroup(array $list)
    {
        //
    }


    public function render()
    {
        $template = $this->getTemplate();

//        $template
//        $template

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}
