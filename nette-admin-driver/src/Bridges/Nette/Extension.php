<?php declare(strict_types=1);

namespace AdminDriver\Bridges\Nette;

use AdminDriver\AdminDriver;
use AdminDriver\Drivers\DibiDriver;
use AdminDriver\Drivers\FileSystemDriver;
use AdminDriver\Drivers\JsonDriver;
use AdminDriver\Drivers\NeonDriver;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package AdminDriver\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'tablePrefix' => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        // each driver must be defined here
        $builder->addDefinition($this->prefix('dibi'))
            ->setFactory(DibiDriver::class, [$config]);

        $builder->addDefinition($this->prefix('neon'))
            ->setFactory(NeonDriver::class, [$config]);

        $builder->addDefinition($this->prefix('json'))
            ->setFactory(JsonDriver::class, [$config]);

        $builder->addDefinition($this->prefix('filesystem'))
            ->setFactory(FileSystemDriver::class, [$config]);

        // + containt in this array
        $drivers = [
            DibiDriver::NAME       => $this->prefix('@dibi'),
            NeonDriver::NAME       => $this->prefix('@neon'),
            JsonDriver::NAME       => $this->prefix('@json'),
            FileSystemDriver::NAME => $this->prefix('@filesystem'),
        ];

        $builder->addDefinition($this->prefix('default'))
            ->setFactory(AdminDriver::class, [$drivers]);
    }
}
