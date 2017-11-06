<?php declare(strict_types=1);

namespace Authorizator\Forms;

use Authorizator\Authorizator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class PrivilegeForm
 *
 * @author  geniv
 * @package Authorizator\Forms
 */
class PrivilegeForm extends Control
{
    /** @var Authorizator */
    private $authorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string template path */
    private $templatePath;

    private $state = null;
    /** @var callback method */
    public $onSuccess, $onError;


    /**
     * Forms constructor.
     *
     * @param Authorizator     $authorizator
     * @param ITranslator|null $translator
     */
    public function __construct(Authorizator $authorizator, ITranslator $translator = null)
    {
        parent::__construct();

        $this->authorizator = $authorizator;
        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/PrivilegeForm.latte';  // default path

        // default onSuccess
        if (!$this->onSuccess) {
            $this->onSuccess[] = function () {
                $this->redirect('this');
            };
        }

        // default onError
        if (!$this->onError) {
            $this->onError[] = function () {
                $this->redirect('this');
            };
        }
    }


    /**
     * Set template path.
     *
     * @param $path
     * @return PrivilegeForm
     */
    public function setTemplatePath($path): self
    {
        $this->templatePath = $path;
        return $this;
    }


    /**
     * Create component form.
     *
     * @param $name
     * @return Form
     */
    protected function createComponentForm($name): Form
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);

        $form->addHidden('id');
        $form->addText('privilege', 'acl-privilegeform-privilege')
            ->setRequired('acl-privilegeform-role-required');
        $form->addText('name', 'acl-privilegeform-name')
            ->setRequired('acl-privilegeform-name-required');

        $form->addSubmit('save', 'acl-privilegeform-save');

        $form->onSuccess[] = function ($form, array $values) {
            if ($this->authorizator->savePrivilege($values)) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
            }
        };
        return $form;
    }


    /**
     * Handle add.
     */
    public function handleAdd()
    {
        $this->state = 'add';
    }


    /**
     * Handle update.
     *
     * @param $id
     */
    public function handleUpdate($id)
    {
        $this->state = 'update';

        $privilege = $this->authorizator->getPrivilege();
        if (isset($privilege[$id])) {
            $this['form']->setDefaults($privilege[$id]);
        }
    }


    /**
     * Handle delete.
     *
     * @param $id
     */
    public function handleDelete($id)
    {
        $privilege = $this->authorizator->getPrivilege();
        if (isset($privilege[$id])) {
            $values = $privilege[$id];

            if ($this->authorizator->savePrivilege(['id' => $id])) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
            }
        }
    }


    /**
     * Render privilege.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->state = $this->state;
        $template->privilege = $this->authorizator->getPrivilege();

        $template->setFile($this->templatePath);
        $template->render();
    }
}
