<?php

namespace App\Model;

use Dibi\Connection;
use Nette\SmartObject;


/**
 * Class Example
 *
 * @author  geniv
 * @package App\Model
 */
class Example
{
    use SmartObject;

    private $tableName;
    /** @var Connection database connection from DI */
    private $connection;


    /**
     * Example constructor.
     *
     * @param            $tableName
     * @param Connection $connection
     */
    public function __construct($tableName, Connection $connection)
    {
        $this->tableName = $tableName;
        $this->connection = $connection;
    }
}
