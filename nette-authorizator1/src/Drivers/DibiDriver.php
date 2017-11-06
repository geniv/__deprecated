<?php declare(strict_types=1);

namespace Authorizator\Drivers;

use Authorizator\Authorizator;
use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;


/**
 * Class DibiDriver
 *
 * @author  geniv
 * @package Authorizator\Drivers
 */
class DibiDriver extends Authorizator
{
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

        parent::__construct($parameters);
    }


    /**
     * Init data.
     *
     * @param array $parameters
     */
    protected function init(array $parameters)
    {
        if ($this->policy != self::POLICY_NONE) {
            // cache role
            $this->role = $this->cache->load('role');
            if ($this->role === null) {
                $this->role = $this->connection->select('id, role, name')
                    ->from($this->tableRole)
                    ->fetchAssoc('id');

                $this->cache->save('role', $this->role);  // cachovani bez expirace
            }

            // cache resource
            $this->resource = $this->cache->load('resource');
            if ($this->resource === null) {
                $this->resource = $this->connection->select('id, resource, name')
                    ->from($this->tableResource)
                    ->fetchAssoc('id');

                $this->cache->save('resource', $this->resource);  // cachovani bez expirace
            }

            // cachce privilege
            $this->privilege = $this->cache->load('privilege');
            if ($this->privilege === null) {
                $this->privilege = $this->connection->select('id, privilege, name')
                    ->from($this->tablePrivilege)
                    ->fetchAssoc('id');

                $this->cache->save('privilege', $this->privilege);  // cachovani bez expirace
            }

            // set role
            foreach ($this->role as $item) {
                $this->permission->addRole($item['role']);
            }

            // set resource
            foreach ($this->resource as $item) {
                if ($item['resource']) {
                    $this->permission->addResource($item['resource']);
                }
            }

            // for deny enable all
            if ($this->policy == self::POLICY_DENY) {
                $this->permission->allow();
            }

            $cursor = $this->connection->select('acl.id,' .
                'role.id id_role, role.role,' .
                'resource.id id_resource, resource.resource,' .
                'privilege.id id_privilege, privilege.privilege')
                ->from($this->tableAcl)->as('acl')
                ->join($this->tableRole)->as('role')->on('role.id=acl.id_role')
                ->leftJoin($this->tableResource)->as('resource')->on('resource.id=acl.id_resource')
                ->leftJoin($this->tablePrivilege)->as('privilege')->on('privilege.id=acl.id_privilege')
                ->where(['acl.active' => true,])
                ->orderBy('acl.position')->asc();

            //$cursor->test();

            // set acl
            foreach ($cursor as $item) {
                $this->acl[] = $item;

                if ($item['role'] && $item['resource'] && $item['privilege']) {
                    if ($this->policy == self::POLICY_ALLOW) {
                        $this->permission->allow($item['role'], $item['resource'], $item['privilege']);
                    } else {
                        $this->permission->deny($item['role'], $item['resource'], $item['privilege']);
                    }
                } else if ($item['role'] && $item['resource']) {
                    if ($this->policy == self::POLICY_ALLOW) {
                        $this->permission->allow($item['role'], $item['resource']);
                    } else {
                        $this->permission->deny($item['role'], $item['resource']);
                    }
                } else if ($item['role']) {
                    if ($this->policy == self::POLICY_ALLOW) {
                        $this->permission->allow($item['role']);
                    } else {
                        $this->permission->deny($item['role']);
                    }
                }
            }
        }
    }


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     */
    public function saveRole(array $values): int
    {
        $id = $values['id'];
        unset($values['id']);

        if (!$id) {
            // add
            return $this->connection->insert($this->tableRole, $values)->execute();
        } else {
            // update
            if ($values) {
                return $this->connection->update($this->tableRole, $values)->where(['id' => $id])->execute();
            } else {
                // delete
                return $this->connection->delete($this->tableRole)->where(['id' => $id])->execute();
            }
        }
    }


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     */
    public function saveResource(array $values): int
    {
        $id = $values['id'];
        unset($values['id']);

        if (!$id) {
            // add
            return $this->connection->insert($this->tableResource, $values)->execute();
        } else {
            // update
            if ($values) {
                return $this->connection->update($this->tableResource, $values)->where(['id' => $id])->execute();
            } else {
                // delete
                return $this->connection->delete($this->tableResource)->where(['id' => $id])->execute();
            }
        }
    }


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     */
    public function savePrivilege(array $values): int
    {
        $id = $values['id'];
        unset($values['id']);

        if (!$id) {
            // add
            return $this->connection->insert($this->tablePrivilege, $values)->execute();
        } else {
            // update
            if ($values) {
                return $this->connection->update($this->tablePrivilege, $values)->where(['id' => $id])->execute();
            } else {
                // delete
                return $this->connection->delete($this->tablePrivilege)->where(['id' => $id])->execute();
            }
        }
    }


    /**
     * Save acl.
     *
     * @param       $idRole
     * @param array $values
     * @return int
     */
    public function saveAcl($idRole, array $values): int
    {
        // delete all acl for idRole
        $res = $this->connection->delete($this->tableAcl)->where(['id_role' => $idRole])->execute();

        if ($values['all']) {
            return $this->connection->insert($this->tableAcl, [
                'id_role' => $idRole,
                'active'  => true,
            ])->execute();
        }

        foreach ($values as $idResource => $item) {
            if (is_array($item)) {
                foreach ($item as $idPrivilege) {
                    $res = $this->connection->insert($this->tableAcl, [
                        'id_role'      => $idRole,
                        'id_resource'  => $idResource,
                        'id_privilege' => ($idPrivilege == 'all' ? null : $idPrivilege),
                        'active'       => true,
                    ])->execute();

                    if (!$res) {
                        return $res;
                    }
                }
            }
        }
        return $res;
    }
}
