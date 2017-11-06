<?php declare(strict_types=1);

namespace Authorizator;

use Nette\Security\IAuthorizator;
use Nette\Security\Permission;
use Nette\SmartObject;


/**
 * Class Authorizator
 *
 * @author  geniv
 * @package Authorizator
 */
abstract class Authorizator implements IAuthorizator
{
    use SmartObject;

    const POLICY_NONE = 'none';
    const POLICY_ALLOW = 'allow';
    const POLICY_DENY = 'deny';

    /** @var string */
    protected $policy;


    /** @var Permission */
    protected $permission;

    protected $role = [], $resource = [], $privilege = [], $acl = [];


    /**
     * Authorizator constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        // allow (all is deny, allow part) | deny (all is allow, deny part) | none (all is allow, ignore part)
        $this->policy = $parameters['policy'];

        $this->permission = new Permission;

        $this->init($parameters);   // init data
    }


    /**
     * Get role.
     *
     * @return array
     */
    public function getRole(): array
    {
        return $this->role;
    }


    /**
     * Get resource.
     *
     * @return array
     */
    public function getResource(): array
    {
        return $this->resource;
    }


    /**
     * Get privilege.
     *
     * @return array
     */
    public function getPrivilege(): array
    {
        return $this->privilege;
    }


    /**
     * Get acl.
     *
     * @param null $idRole
     * @param null $idResource
     * @return array
     */
    public function getAcl($idRole = null, $idResource = null): array
    {
        if ($idRole) {
            $callback = function ($row) use ($idRole, $idResource) {
                if ($idRole && $idResource) {
                    return $row['id_role'] == $idRole && $row['id_resource'] == $idResource;
                }
                if ($idRole) {
                    return $row['id_role'] == $idRole;
                }
                return true;
            };
            return array_filter($this->acl, $callback);
        }
        return $this->acl;
    }


    /**
     * Is all.
     *
     * @param      $idRole
     * @param null $idResource
     * @return bool
     */
    public function isAll($idRole, $idResource = null): bool
    {
        $acl = $this->getAcl($idRole);
        if ($idResource) {
            $callback = function ($row) use ($idResource) {
                if ($idResource) {
                    return $row['id_resource'] == $idResource;
                }
                return true;
            };
            $res = array_values(array_filter($acl, $callback));
            if (isset($res[0])) {
                return $res[0]['id_privilege'] == self::ALL;
            }
        }

        $aclAll = array_values($acl);
        if (isset($aclAll[0])) {
            return $aclAll[0]['id_resource'] == self::ALL && $aclAll[0]['id_privilege'] == self::ALL;
        }
        return false;
    }


    /**
     * Get permission.
     *
     * @return Permission
     */
    public function getPermission(): Permission
    {
        return $this->permission;
    }


    /**
     * Init data.
     *
     * @param array $parameters
     */
    abstract protected function init(array $parameters);


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     */
    abstract public function saveRole(array $values): int;


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     */
    abstract public function saveResource(array $values): int;


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     */
    abstract public function savePrivilege(array $values): int;


    /**
     * Save acl.
     *
     * @param       $role
     * @param array $values
     * @return int
     */
    abstract public function saveAcl($role, array $values): int;


    /**
     * Performs a role-based authorization.
     *
     * @param $role
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege): bool
    {
        if ($this->policy == self::POLICY_NONE) {
            return true;
        }
        return $this->permission->isAllowed($role, $resource, $privilege);
    }
}
