<?php declare(strict_types=1);

namespace Authorizator\Forms;

use Authorizator\Authorizator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class AclForm
 *
 * @author  geniv
 * @package Authorizator\Forms
 */
class AclForm extends Control
{
    /** @var Authorizator */
    private $authorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string template path */
    private $templatePath;

    private $idRole = null;
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

        $this->templatePath = __DIR__ . '/AclForm.latte';  // default path

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
     * @return AclForm
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

        $form->addHidden('idRole');


        $items = array_map(function ($row) { return $row['privilege']; }, $this->authorizator->getPrivilege());
        $items['all'] = 'all';

        $form->addGroup('acl-aclform-group-all');
        $form->addCheckbox('all', 'acl-aclform-all');

        foreach ($this->authorizator->getResource() as $item) {
            $form->addGroup('acl-aclform-group-' . $item['resource']);

            //'acl-aclform-' . $item['resource']
            $form->addCheckboxList($item['id'])
                ->setItems($items)
                ->setTranslator(null);
        }
        $form->addGroup();

        $form->addSubmit('save', 'acl-aclform-save');

        $form->onSuccess[] = function ($form, array $values) {
            $idRole = $values['idRole'];
            unset($values['idRole']);

            if ($this->authorizator->saveAcl($idRole, $values)) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
            }
        };
        return $form;
    }


    /**
     * Handle update.
     *
     * @param $id
     */
    public function handleUpdate($id)
    {
        $this->idRole = $id;

        $defaultItems = [];
        foreach ($this->authorizator->getResource() as $item) {
            $acl = $this->authorizator->getAcl($id, $item['id']);

            if ($this->authorizator->isAll($id, $item['id'])) {
                // idRole, idResource, ALL
                $defaultItems[$item['id']] = 'all';
            } else {
                $defaultItems[$item['id']] = array_values(array_map(function ($row) { return $row['id_privilege']; }, $acl));
            }
        }

        if ($this->authorizator->isAll($id)) {
            // idRole, ALL, ALL
            $defaultItems['all'] = true;
        }
        $this['form']->setDefaults(['idRole' => $id] + $defaultItems);
    }


    /**
     * Render.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->role = $this->authorizator->getRole();
        $template->idRole = $this->idRole;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}
