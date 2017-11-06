ACL
===

Installation
------------

```sh
$ composer require geniv/nette-authorizator
```
or
```json
"geniv/nette-authorizator": ">=1.0.0"
```

require:
```json
"php": ">=5.6.0",
"nette/nette": ">=2.4.0",
"dibi/dibi": ">=3.0.0"
```

Include in application
----------------------

policy:
- `allow` - all is deny, allow part
- `deny` - all is allow, deny part
- `none` - all is allow, ignore part

neon configure:
```neon
# acl
authorizator:
	policy: allow
	tablePrefix: %tablePrefix%
```

neon configure extension:
```neon
extensions:
    authorizator: Authorizator\Bridges\Nette\Extension
```

presenters:
```php
$acl = $this->user->getAuthorizator();
$acl->isAllowed('guest', 'sekce-forum', 'zobrazit');

$this->user->isAllowed('sekce-forum', 'zobrazit');
```

usage:
```latte
$user->isAllowed('sekce-forum', 'zobrazit')
```
