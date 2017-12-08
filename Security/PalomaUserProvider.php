<?php


namespace Paloma\ClientBundle\Security;


use Paloma\ClientBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PalomaUserProvider implements UserProviderInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function loadUserByUsername($username)
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UsernameNotFoundException();
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface) || $username != $user->getUsername()) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return $class instanceof User;
    }

}
