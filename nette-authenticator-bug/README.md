Security user authenticator
===========================

Installation
------------

```sh
$ composer require geniv/nette-authenticator
```
or
```json
"geniv/nette-authenticator": ">=1.0.0"
```

require:
```json
"php": ">=5.6.0",
"nette/nette": ">=2.4.0",
"dibi/dibi": ">=3.0.0"
```

Include in application
----------------------
neon configure:
```neon
# prihlasovani
authenticator:
    tablePrefix: %tablePrefix%
```

neon configure extension:
```neon
extensions:
    authenticator: Authenticator\Bridges\Nette\Extension
```

presenters:
```php
use Authenticator\LoginForm;

protected function createComponentLoginForm(LoginForm $loginForm)
{
    //$loginForm->setTemplatePath(__DIR__ . '/templates/LoginForm.latte');
    // callback from $loginForm (redirect is no problem)
    $loginForm->onLoggedIn[] = function (User $user) {
        $this->flashMessage('Login!', 'info');
        $this->redirect('this');
    };
    $loginForm->onLoggedInException[] = function (Exception $e) {
        $this->flashMessage('Login exception! ' . $e->getMessage(), 'danger');
    };
    $loginForm->onLoggedOut[] = function (User $user) {
        $this->flashMessage('Logout!', 'info');
        $this->redirect('this');
    };

    // callback from $this->user (dont use redirect)
    $this->user->onLoggedIn[] = function (User $user) {
        $this->flashMessage('Login!', 'info');
    };
    $this->user->onLoggedOut[] = function (User $user) {
        $this->flashMessage('Logout!', 'info');
    };
    return $loginForm;
}
```

usage:
```latte
    {if !$user->isLoggedIn()}
        {control loginForm}
    {else}
        <a n:href="loginForm:Out!">Logout</a>
    {/if}
```
