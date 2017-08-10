Button macros (cc) from Pavel Železný (2bfree), 2013 [pavelzelezny.cz](http://pavelzelezny.cz), edit by geniv, 2014, 2017
===========================================

Requirements
------------

geniv/nette-button-macros requires PHP 5.3.x edition or higher.

- [Nette Framework](https://github.com/nette/nette)


Documentation
-------------

Add support of pair latte macro "button" for render form control SubmitButton as &lt;button&gt;. It can be useful for Twitter bootstrap design.

Instalation
-----------

```sh
$ composer require geniv/nette-button-macros
```

Prefered way to intall is by [Composer](http://getcomposer.org)

composer.json:
```json
"geniv/nette-button-macros": ">= 1.0"
```

Setup
-----

Add following code into config.neon

```neon
latte:
    macros:
        - ButtonMacros::install
```

Using
-----

In php form component standart submit 

```php
$form->addSubmit('send', 'Caption');
```

In latte template you can use following code

```latte
{form formName}
    {button controlName class=>"btn"}
        <i class="icon icon-ok"></i>
        {caption}
    {/button}
{/form}
```

Also you can use object instead the control name

```latte
{form formName}
    {button $form['controlName'] class=>"btn"}
        <i class="icon icon-ok"></i>
        {caption}
    {/button}
{/form}
```
