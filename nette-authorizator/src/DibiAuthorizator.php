<?php

namespace Authorizator;

use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Security\IAuthorizator;
use Nette\SmartObject;


/**
 * Class DibiAuthorizator
 *
 * @author  geniv
 * @package Authorizator
 */
class DibiAuthorizator implements IAuthorizator
{
    use SmartObject;

    // define constant table names
    const
        TABLE_NAME = 'acl',
        TABLE_NAME_PRIVILEGE = 'acl_privilege',
        TABLE_NAME_RESOURCE = 'acl_resource',
        TABLE_NAME_ROLE = 'acl_role';

    /** @var Cache */
    private $cache;
    /** @var Connection database connection from DI */
    private $connection;
    /** @var string table names */
    private $tableAcl, $tablePrivilege, $tableResource, $tableRole;

    private $policy;
    private $role, $resource, $privilege;


    /**
     * DibiAuthorizator constructor.
     *
     * @param array      $parameters
     * @param Connection $connection
     */
    public function __construct(array $parameters, Connection $connection, IStorage $storage)
    {
        $this->connection = $connection;
        $this->cache = new Cache($storage, 'cache-Authorizator-DibiAuthorizator');
        // define table names
        $this->tableAcl = $parameters['tablePrefix'] . self::TABLE_NAME;
        $this->tablePrivilege = $parameters['tablePrefix'] . self::TABLE_NAME_PRIVILEGE;
        $this->tableResource = $parameters['tablePrefix'] . self::TABLE_NAME_RESOURCE;
        $this->tableRole = $parameters['tablePrefix'] . self::TABLE_NAME_ROLE;

        $this->policy = $parameters['policy'];  // allow (all is deny, allow part) | deny (all is allow, deny part) | none (all is allow, ignore part)

        $this->init();
    }


    /**
     * Init.
     */
    private function init()
    {
        if ($this->policy != 'none') {
            // cache role
            $this->role = $this->cache->load('role');
            if ($this->role === null) {
                $this->role = $this->connection->select('id, role')
                    ->from($this->tableRole)
                    ->fetchPairs('role', 'id');

                $this->cache->save('role', $this->role);  // cachovani bez expirace
            }

            // cache resource
            $this->resource = $this->cache->load('resource');
            if ($this->resource === null) {
                $this->resource = $this->connection->select('id, resource')
                    ->from($this->tableResource)
                    ->fetchPairs('resource', 'id');

                $this->cache->save('resource', $this->resource);  // cachovani bez expirace
            }

            // cachce privilege
            $this->privilege = $this->cache->load('privilege');
            if ($this->privilege === null) {
                $this->privilege = $this->connection->select('id, privilege')
                    ->from($this->tablePrivilege)
                    ->fetchPairs('privilege', 'id');

                $this->cache->save('privilege', $this->privilege);  // cachovani bez expirace
            }
        }
    }


    /**
     * Performs a role-based authorization.
     *
     * @param  string|null
     * @param  string|null
     * @param  string|null
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege)
    {
        if ($this->policy == 'none') {
            return true;
        }
        // cachce privilege
        $ret = $this->cache->load('acl' . $role . $resource . $privilege);
        if ($ret === null) {
            $res = $this->connection->select('id')
                ->from($this->tableAcl)
                ->where(['active' => true,])
                ->orderBy('position')->desc();

            if ($role) {
                $res->where(['id_role' => $this->role[$role]]);
            }
            if ($resource) {
                $res->where(['id_resource' => $this->resource[$resource]]);
            }
            if ($privilege) {
                $res->where(['id_privilege' => $this->privilege[$privilege]]);
            }
//TODO otestovat cache!
            $ret = $res->fetch();
            $this->cache->save('acl' . $role . $resource . $privilege, $ret);  // cachovani bez expirace
        }

        if ($ret) {
            $allow = ($ret->id > 0);
            if ($this->policy == 'deny') {
                $allow = !$allow;
            }
            return $allow;
        }
        return ($this->policy == 'deny');
    }
}
