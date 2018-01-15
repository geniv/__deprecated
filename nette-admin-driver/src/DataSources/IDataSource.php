<?php declare(strict_types=1);

namespace AdminDriver\DataSources;

/**
 * Interface IDataSource
 *
 * @author  geniv
 * @package AdminDriver\DataSources
 */
interface IDataSource
{
    public function getCount(): int;


    public function getData(): array;


    public function limit($offset, $limit);


    public function sort(array $sorting);


//    public function filter(array $condition);
//    public function suggest($column, array $conditions, $limit);
}
