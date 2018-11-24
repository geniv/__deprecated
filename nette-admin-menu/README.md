Admin element
=============

Installation
------------

```sh
$ composer require geniv/nette-check-browser
```
or
```json
"geniv/nette-check-browser": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"geniv/nette-general-form": ">=1.0.0"
```

Include in application
----------------------

neon configure:
```neon
services:
    - CheckBrowser
```

presenters:
```php
protected function createComponentCheckBrowser(CheckBrowser $checkBrowser): CheckBrowser
{
    return $checkBrowser;
}
```

usage:
```latte
{*after body*}
{control checkBrowser}
```
