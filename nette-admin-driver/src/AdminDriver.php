<?php declare(strict_types=1);

namespace AdminDriver;

use AdminDriver\Drivers\IDriver;


/**
 * Class AdminDriver
 *
 * @author  geniv
 * @package AdminDriver
 */
class AdminDriver
{
    private $drivers;


    /**
     * AdminDriver constructor.
     *
     * @param $drivers
     */
    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }


    /**
     * Get list driver.
     *
     * @return array
     */
    public function getListDriver(): array
    {
        return array_map(function ($row) {
            return get_class($row);
        }, $this->drivers);
    }


    /**
     * Get driver.
     *
     * @param $driver
     * @return bool|self
     */
    public function getDriver($driver)
    {
        if (isset($this->drivers[$driver])) {
            return $this->drivers[$driver];
        }
        return false;
    }


    //TODO doresit!!!
    public function factory(array $configure): IDriver
    {
        return $this->getDriver($configure['driver']['type'])->factory($configure);
    }
}
