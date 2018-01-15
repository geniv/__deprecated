<?php declare(strict_types=1);

namespace AdminDriver\DataSources;


/**
 * Class DibiDataSource
 *
 * @author  geniv
 * @package AdminDriver\DataSources
 */
class DibiDataSource implements IDataSource
{

    public function __construct(array $parameters)
    {
        //
    }


    public function getCount(): int
    {
        // TODO: Implement getCount() method.
    }


    public function getData(): array
    {
        // TODO: Implement getData() method.
    }


    public function limit($offset, $limit)
    {
        // TODO: Implement limit() method.
    }


    public function sort(array $sorting)
    {
        // TODO: Implement sort() method.
    }
}
