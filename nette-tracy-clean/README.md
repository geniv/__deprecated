tracy:
	bar: # panely do Debugger baru
		- TracyClean\Bridges\Tracy\Panel

base presenter, under autowire
    use TracyClean;


	tracyClean: TracyClean\Bridges\Nette\Extension
#	- TracyClean\Bridges\Nette\Extension


tracyClean:
	link:
		Old clean:
			url: "clean.php"
			target: _blank
		Admin new window:
			url: "admin/"
			target: _blank
		Admin2:
			url: "admin/"
			target: _self
		Admin: "admin/"
		pokus:
			link: Homepage:vzor





Google analytics
================
Google analytics & Google Tag Manager component

Installation
------------
```sh
$ composer require geniv/nette-analytics
```
or
```json
"geniv/nette-analytics": ">=1.0.0"
```

require:
```json
"php": ">=5.6.0",
"nette/nette": ">=2.4.0"
```


class BasePresenter extends Presenter
{
    use AutowiredComponent;
    use TracyClean;
    
    
    

Include in application
----------------------
neon configure:
```neon
# google analytics
analytics:
#   productionMode: true
#   async: true
    ga: 'UA-XXXXX-Y'
#   ga:
#       cs: 'UA-XXXXX-Y'
    gtm: 'GTM-XXXXXXX'
#   gtm:
#       cs: 'GTM-XXXXXXX'
```

neon configure extension:
```neon
extensions:
    analytics: Analytics\Bridges\Nette\Extension
```

base presenters:
```php
use Analytics\GoogleGa;

protected function createComponentGa(GoogleGa $googleGa)
{
    //return $googleGa->setLocaleCode($this->locale);
    return $googleGa;
}

use Analytics\GoogleTagManager;

protected function createComponentGtm(GoogleTagManager $googleTagManager)
{
    //return $googleTagManager->setLocaleCode($this->locale);
    return $googleTagManager;
}
```

usage:
```latte
{*high in the <head>*}
{control ga}

{*high in the <head>*}
{control gtm}

{*after the opening <body> tag*}
{control gtm:body}
```
