<?php declare(strict_types=1);

namespace Authorizator\Drivers;

use Nette\Neon\Neon;


/**
 * Class NeonDriver
 *
 * @author  geniv
 * @package Authorizator\Drivers
 */
class NeonDriver extends ArrayDriver
{
    private $path = null;
    private $data = null;


    /**
     * NeonDriver constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->setPath($parameters['path']);

        parent::__construct($parameters);
    }


    /**
     * Set path.
     *
     * @param $path
     * @return NeonDriver
     */
    public function setPath($path): self
    {
        $this->path = $path;
        return $this;
    }


    /**
     * Init data.
     *
     * @param array $parameters
     */
    protected function init(array $parameters)
    {
        if ($this->path && file_exists($this->path)) {
            $this->data = Neon::decode(file_get_contents($this->path));

            parent::init(array_merge($parameters, $this->data));
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
            $role = $this->data['role'];
            $role[$values['role']] = $values['name'];
            $this->data['role'] = $role;
        } else {
            // update
            if ($values) {
                $role = $this->data['role'];
                $role[$values['role']] = $values['name'];
                $this->data['role'] = $role;
            } else {
                // delete
                unset($this->data['role'][$id]);
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
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
            $resource = $this->data['resource'];
            $resource[$values['resource']] = $values['name'];
            $this->data['resource'] = $resource;
        } else {
            // update
            if ($values) {
                $resource = $this->data['resource'];
                $resource[$values['resource']] = $values['name'];
                $this->data['resource'] = $resource;
            } else {
                // delete
                unset($this->data['resource'][$id]);
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
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
            $privilege = $this->data['privilege'];
            $privilege[$values['privilege']] = $values['name'];
            $this->data['privilege'] = $privilege;
        } else {
            // update
            if ($values) {
                $privilege = $this->data['privilege'];
                $privilege[$values['privilege']] = $values['name'];
                $this->data['privilege'] = $privilege;
            } else {
                // delete
                unset($this->data['privilege'][$id]);
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
    }


    /**
     * Save acl.
     *
     * @param       $role
     * @param array $values
     * @return int
     */
    public function saveAcl($role, array $values): int
    {
        unset($this->data['acl'][$role]);

        if (isset($values['all']) && $values['all']) {
            $this->data['acl'][$role] = 'all';
            return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
        }

        foreach ($values as $idResource => $item) {
            if ($item && is_array($item)) {
                if (!in_array('all', $item)) {
                    $this->data['acl'][$role][$idResource] = $item;
                } else {
                    $this->data['acl'][$role][$idResource][] = 'all';
                }
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
    }
}
