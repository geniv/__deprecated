<?php

/**
 * Class LoginFormAuthenticator
 *
 * @author  geniv
 * @package NetteWeb
 */
class LoginFormAuthenticator implements Nette\Security\IAuthenticator
{
    use Nette\SmartObject;

    private $identityModel;


    /**
     * LoginFormAuthenticator constructor.
     * @param \App\Model\Identity $identity
     */
    public function __construct(\App\Model\Identity $identity)
    {
        $this->identityModel = $identity;
    }


    /**
     * authentikace uzivatele
     * @param array $credentials
     * @return \Nette\Security\Identity
     * @throws \Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;

        $cursor = $this->identityModel->isAuthenticate($login, $password);

        if ($cursor) {
            if ($cursor->Active) {
                $arr = $cursor->toArray();
                return new Nette\Security\Identity($cursor->Id, $cursor->Role, $arr);
            } else {
                throw new Nette\Security\AuthenticationException('Neschválený účet!', self::NOT_APPROVED);
            }
        } else {
            throw new Nette\Security\AuthenticationException('Neplatné údaje!', self::INVALID_CREDENTIAL);
        }
    }


    /**
     * rucni pridavani uzivatelu
     * @param $username
     * @param $password
     * @return string
     */
    public function add($username, $password)
    {
        return 'insert user with Id: ' . $this->identityModel->insertUser($username, $password);
    }
}
