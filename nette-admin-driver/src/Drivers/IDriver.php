<?php declare(strict_types=1);

namespace AdminDriver\Drivers;

/**
 * Interface IDriver
 *
 * @author  geniv
 * @package AdminDriver\Drivers
 */
interface IDriver
{
    /**
     * Factory.
     *
     * @param array $configure
     * @return IDriver
     */
    public function factory(array $configure): IDriver;


    /**
     * Insert.
     *
     * @param $values
     * @return int
     */
    public function insert($values): int;


    /**
     * Update.
     *
     * @param int   $id
     * @param array $values
     * @return int
     */
    public function update(int $id, array $values): int;


    /**
     * Delete.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int;
}
