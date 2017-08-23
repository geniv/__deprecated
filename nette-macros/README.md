Macros
======
Latte macros

Installation
------------

```sh
$ composer require geniv/nette-macros
```
or
```json
"geniv/nette-macros": ">=1.0.0"
```

require:
```json
"php": ">=5.6.0",
"nette/nette": ">=2.4.0"
```

Include in application
----------------------

neon configure:
```neon
extensions:
    macros: Macros\Bridges\Nette\Extension
```

neon configure extension:
```neon
# macros
macros:
    IfCurrent: false
    IfCurrentIn: true
    IfCurrentSwitch: false
    SubmitButton: false
    Confirm: true
```

IfCurrentIn:
```latte
<div n:ifCurrentIn="'News:default', 'Homepage:default'">Hello, n:macro</div>
{ifCurrentIn 'News:default', 'Homepage:default'}Hello, standart macro{/ifCurrentIn}
```

SubmitButton:
```latte
{form formName}
    {button controlName class=>"btn"}
        <i class="icon icon-ok"></i>
        {caption}
    {/button}
{/form}

{form formName}
	{button $_form['controlName'] class=>"btn"}
		<i class="icon icon-ok"></i>
		{caption}
	{/button}
{/form}
```

Confirm:
```latte
<a href="..." {confirm 'Really delete?'}>delete</a>
```
