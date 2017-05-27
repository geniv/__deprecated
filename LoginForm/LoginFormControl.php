<?php
use Nette\Localization\ITranslator;

/**
 * Class LoginFormControl
 *
 * @author  geniv
 * @package NetteWeb
 */
class LoginFormControl extends Nette\Application\UI\Control
{
    private $translator;
    private $pathTemplate;
    private $redirectIn, $redirectOut;


    /**
     * LoginFormControl constructor.
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator = null)
    {
        parent::__construct();
        $this->translator = $translator;

        // nastaveni implcitni cesty
        $this->pathTemplate = __DIR__ . '/LoginForm.latte';
        $this->redirectIn = 'Homepage:';
        $this->redirectOut = 'Homepage:';
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
     * nastavovani pro redirect vstupu a vystupu
     * @param $in
     * @param $out
     * @return $this
     */
    public function setRedirect($in, $out)
    {
        $this->redirectIn = $in;
        $this->redirectOut = $out;
        return $this;
    }


    /**
     * render komponenty
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile($this->pathTemplate);
        $template->render();
    }


    /**
     * komponenta formulare
     * @param  [type] $name [description]
     * @return Nette\Application\UI\Form
     */
    protected function createComponentForm($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
        $form->setTranslator($this->translator);
        $form->addText('username', 'Username')
            ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Password')
            ->setRequired('Please enter your password.');

        $form->addSubmit('send', 'Login');

        $form->onSuccess[] = [$this, 'formSuccess'];
        return $form;
    }


    /**
     * success callback formulare
     * @param \Nette\Application\UI\Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function formSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $presenter = $this->getPresenter();
        try {
            $presenter->user->login($values->username, $values->password);
            $presenter->redirect($this->redirectIn);
        } catch (Nette\Security\AuthenticationException $e) {
            $presenter->flashMessage($e->getMessage(), 'error');
        }
    }


    /**
     * signal pro odhlaseni
     */
    public function handleOut()
    {
        $presenter = $this->getPresenter();
        $presenter->user->logout(true);
        $presenter->flashMessage($this->translator->translate('Odhlášení bylo úspěšné.'), 'info');
        $presenter->redirect($this->redirectOut);
    }
}
