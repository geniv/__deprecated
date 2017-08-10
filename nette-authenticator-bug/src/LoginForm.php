<?php

namespace Authenticator;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Application\UI\Control;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;


/**
 * Class LoginForm
 *
 * @author  geniv
 * @package Authenticator
 */
class LoginForm extends Control
{
    /** @var ITranslator|null */
    private $translator;
    /** @var string template path */
    private $templatePath;
    /** @var callback method */
    public $onLoggedIn, $onLoggedInException, $onLoggedOut;


    /**
     * LoginForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator = null)
    {
        parent::__construct();

        $this->translator = $translator;
        // nastaveni implcitni cesty
        $this->templatePath = __DIR__ . '/LoginForm.latte';
    }


    /**
     * Set template path.
     *
     * @param string $path
     * @return $this
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
        return $this;
    }


    /**
     * Render default.
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }


    /**
     * Create component login form with success callback.
     *
     * @param $name
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);
        $form->addText('username', 'login-form-username')
            ->setRequired('login-form-username-required');

        $form->addPassword('password', 'login-form-password')
            ->setRequired('login-form-password-required');

        $form->addSubmit('send', 'login-form-send');

        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $user = $this->getPresenter()->getUser();
                $user->login($values->username, $values->password);
                if ($user->isLoggedIn()) {
                    $this->onLoggedIn($user);   // success callback
                }
            } catch (AuthenticationException $e) {
                $this->onLoggedInException($e); // exception callback
            }
        };
        return $form;
    }


    /**
     * Ajax handler for logout.
     */
    public function handleOut()
    {
        $user = $this->getPresenter()->getUser();
        $user->logout(true);
        if (!$user->isLoggedIn()) {
            $this->onLoggedOut($user);  // logout callback
        }
    }
}
