Seo title and description
=========================

description: block title and description are internal save to database

Installation
------------

```sh
$ composer require geniv/nette-seo
```
or
```json
"geniv/nette-seo": ">=1.0.0"
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
# seo
seo:
    tablePrefix: %tablePrefix%
```

neon configure extension:
```neon
extensions:
    seo: Seo\Bridges\Nette\Extension
```

usage @layout.latte:
```latte
<title>{ifset title}{include title|seoTitle} | {/ifset}default title</title>
<meta name="description" content="{ifset description}{include description|seoDescription} | {/ifset}default description">
```

###Warning:
text ident in title and description is automatic translate!!!
```latte
{block title}homepage-title{/block}
{block description}homepage-description{/block}
```
