<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class UserManager extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function returnManyUsers($users_array)
    {
        $result = array();
        $row = 0;

        foreach($users_array as $value) {

            $result[$row]['id'] = $value->getId();
            $result[$row]['user_name'] = $value->getUsername();
            $result[$row]['user_mail'] = $value->getEmail();
            $result[$row]['user_role'] = $value->getRoles();
            $result[$row]['user_active'] = $value->getActive();
            $result[$row]['user_create_date'] = $value->getUserCreateDate();
            $result[$row]['user_api'] = $value->getApiKey();
            $result[$row]['user_photo'] = $value->getPhoto();

            $row++;
        }

        return $result;
    }

    public function returnOneUser($user)
    {
        $result = array();

        $result['id'] = $user->getId();
        $result['user_name'] = $user->getUsername();
        $result['user_mail'] = $user->getEmail();
        $result['user_role'] = $user->getRoles();
        $result['user_active'] = $user->getActive();
        $result['user_create_date'] = $user->getUserCreateDate();
        $result['user_api'] = $user->getApiKey();
        $result['user_photo'] = $user->getPhoto();

        return $result;
    }

    public function validator($object_validate)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($object_validate);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;

            throw new HttpException(422, $errorsString);
        }
    }

    public function hashPassword($user, $user_password)
    {
        $hash = $this->container->get('security.password_encoder')->encodePassword($user, $user_password);

        return $hash;
    }

    public function createNewSuperUser($username, $email, $active, $role, $apiKey, $password)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setUserCreateDate(new \DateTime('now'));
        $user->setEmail($email);
        $user->setActive($active);
        $user->setRoles($role);
        $user->setApiKey($apiKey);
        $user->setPassword($this->hashPassword($user, $password));

        $this->validator($user);

        $this->getDoctrine()->getRepository('AppBundle:User')->saverObject($user);
    }

    public function createNewUser($username, $email, $password)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setUserCreateDate(new \DateTime('now'));
        $user->setEmail($email);
        $user->setActive(0);
        $user->setRoles('ROLE_USER');
        $random_api = bin2hex(random_bytes(32));
        $user->setApiKey($random_api);
        $user->setPassword($this->hashPassword($user, $password));

        $this->validator($user);

        $this->getDoctrine()->getRepository('AppBundle:User')->saverObject($user);

        $heading = 'Hi, friend!';
        $from = 'grandShushpanchik@gmail.com';
        $setTo = $email;
        $text_message = 'Welcome! Link for activation your account:  ' .'http://'. $_SERVER['HTTP_HOST']. '/activation?apikey=' . $random_api;

        $this->get('my_mailer')->sendEmail($heading, $from, $setTo, $text_message);
    }

    public function userActivation($apiKey)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        
    }
}