<?php declare(strict_types=1);

namespace Authorizator\Bridges\Nette;

use Authorizator\Drivers\ArrayDriver;
use Authorizator\Drivers\DibiDriver;
use Authorizator\Drivers\NeonDriver;
use Authorizator\Forms\AclForm;
use Authorizator\Forms\PrivilegeForm;
use Authorizator\Forms\ResourceForm;
use Authorizator\Forms\RoleForm;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Authorizator\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'autowired'   => null,
        'policy'      => 'allow',   // allow|deny|none
        'source'      => null,  // Array|Neon|Dibi
        'tablePrefix' => null,
        'path'        => null,
        'role'        => null,
        'resource'    => null,
        'privilege'   => null,
        'acl'         => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        // define driver
        switch ($config['source']) {
            case 'Array':
                $builder->addDefinition($this->prefix('default'))
                    ->setFactory(ArrayDriver::class, [$config]);
                break;

            case 'Neon':
                $builder->addDefinition($this->prefix('default'))
                    ->setFactory(NeonDriver::class, [$config]);
                break;

            case 'Dibi':
                $builder->addDefinition($this->prefix('default'))
                    ->setFactory(DibiDriver::class, [$config]);
                break;
        }

        $builder->addDefinition($this->prefix('form.role'))
            ->setFactory(RoleForm::class);

        $builder->addDefinition($this->prefix('form.resource'))
            ->setFactory(ResourceForm::class);

        $builder->addDefinition($this->prefix('form.privilege'))
            ->setFactory(PrivilegeForm::class);

        $builder->addDefinition($this->prefix('form.acl'))
            ->setFactory(AclForm::class);

        // if define autowired then set value
        if (isset($config['autowired'])) {
            $builder->getDefinition($this->prefix('default'))
                ->setAutowired($config['autowired']);
        }
    }
}
