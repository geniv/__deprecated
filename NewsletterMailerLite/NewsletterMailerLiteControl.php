<?php
use Nette\Localization\ITranslator;

/**
 * Class NewsletterMailerLiteControl
 * komponenta pro newsltery sluzby: https://www.mailerlite.com/
 *
 * composer:
 * $ php composer.phar require mailerlite/mailerlite-api-v2-php-sdk
 *
 * @author  geniv
 */
class NewsletterMailerLiteControl extends Nette\Application\UI\Control
{
    private $translator, $groupsApi, $groupId;


    /**
     * NewsletterMailerLiteControl constructor.
     * @param \Nette\ComponentModel\IContainer $configure
     * @param ITranslator $translator
     */
    public function __construct($configure, ITranslator $translator=null)
    {
        parent::__construct();
        $this->groupsApi = (new \MailerLiteApi\MailerLite($configure['api']))->groups();
        $this->translator = $translator;
    }


    /**
     * komponenta formulare
     * @param $name
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentForm($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
        $form->setTranslator($this->translator);
        $form->addText('email', 'Váš E-mail')
            ->setRequired('Pole emailu musí být vyplněno.')
            ->addRule(Nette\Forms\Form::EMAIL, 'Musí být zadaný validní email.')
            ->setAttribute('autocomplete', 'off');
        $form->addHidden('groupId', $this->groupId);    // prenaseni id skupiny pro mailer lite
        $form->addSubmit('send', 'Odeslat');
        $form->onSuccess[] = [$this, 'processOnSuccessForm'];
        return $form;
    }


    /**
     * zpracovani formulare
     * @param \Nette\Application\UI\Form $form
     * @param array $values
     */
    public function processOnSuccessForm(Nette\Application\UI\Form $form, array $values)
    {
        $subscriber = [
            'email' => $values['email'],
//            'fields' => [
//                'name' => 'John',
//                'surname' => 'Doe',
//                'company' => 'MailerLite'
//            ]
        ];
        $addedSubscriber = $this->groupsApi->addSubscriber($values['groupId'], $subscriber); // returns added subscriber
        if (!isset($addedSubscriber->error)) {
            $this->parent->flashMessage($this->translator->translate('Váš email byl uložen.'), 'success');
            $this->parent->redirect('//this');
        }
    }


    /**
     * render komponenty
     * @param $groupId
     */
    public function render($groupId)
    {
        $this->groupId = $groupId;
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile(__DIR__ . '/NewsletterMailerLite.latte');
        $template->render();
    }
}
