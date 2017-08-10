<?php

namespace Authenticator;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Passwords;
use Nette\Security\Identity;
use Nette\SmartObject;


/**
 * Class DatabaseAuthenticator
 *
 * @author  geniv
 * @package Authenticator
 */
class DatabaseAuthenticator implements IAuthenticator
{
    use SmartObject;

    // define constant table names
    const
        TABLE_NAME = 'identity';

    /** @var Connection database connection from DI */
    private $connection;
    /** @var string table names */
    private $tableIdentity;


    /**
     * DatabaseIdentity constructor.
     *
     * @param array      $parameters
     * @param Connection $connection
     */
    public function __construct(array $parameters, Connection $connection)
    {
        $this->connection = $connection;
        // define table names
        $this->tableIdentity = $parameters['tablePrefix'] . self::TABLE_NAME;
    }


    /**
     * Get list users.
     *
     * @return Fluent
     */
    public function getList()
    {
        return $this->connection->select('id, login, hash, username, email, role, active, added')
            ->from($this->tableIdentity);
    }


    /**
     * Insert user.
     *
     * @param        $username
     * @param        $password
     * @param string $role
     * @param bool   $active
     * @return mixed
     */
    public function insertUser($username, $password, $role = 'guest', $active = true)
    {
        $args = [
            'login'     => $username,
            'hash'      => $this->getHash($password),
            'role'      => $role,
            'active'    => $active,
            'added%sql' => 'NOW()',
        ];
        return $this->connection->insert($this->tableIdentity, $args)->execute();
    }


    /**
     * Delete user.
     *
     * @param $id
     * @return Result|int
     */
    public function deleteUser($id)
    {
        return $this->connection->delete($this->tableIdentity)
            ->where(['id' => $id])
            ->execute();
    }


    /**
     * Get password hash.
     *
     * @param $password
     * @return mixed
     */
    public function getHash($password)
    {
        return Passwords::hash($password);
    }


    /**
     * Authenticate user.
     *
     * @param array $credentials
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;

        $cursor = $this->getList()
            ->where(['login' => $login, 'active' => true])
            ->fetch();

        if ($cursor) {
            if (Passwords::verify($password, $cursor->hash)) {
                if ($cursor->active) {
                    $arr = $cursor->toArray();
                    unset($arr['hash']);

                    return new Identity($cursor->id, $cursor->role, $arr);
                } else {
                    throw new AuthenticationException('Not active account.', self::NOT_APPROVED);
                }
            } else {
                throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
            }
        } else {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        }
    }
}
