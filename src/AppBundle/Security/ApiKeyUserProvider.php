<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiKeyUserProvider implements UserProviderInterface
{
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getUsernameForApiKey($apiKey)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('apiKey' => $apiKey));

        if(!$user){

            throw new HttpException(401, 'ApiKey not valid!');
        }

        if ($user->getActive() != 1) {

            throw new HttpException(401, 'User not active!');
        }

        return $user;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => $username));

        $user_role = $user->getRoles();

        return new User(
            $username,
            null,
            array($user_role)
        );
    }

    public function refreshUser(UserInterface $user)
    {

        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}