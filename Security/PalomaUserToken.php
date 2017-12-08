<?php


namespace Paloma\ClientBundle\Security;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class PalomaUserToken extends AbstractToken
{

    /** @var  string */
    private $password;

    public function getCredentials()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return PalomaUserToken
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

}
