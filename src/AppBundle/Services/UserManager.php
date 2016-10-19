<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;

class UserManager extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function returnManyUsers()
    {
        $all_users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        if(empty($all_users)){

            throw new HttpException(204, 'Users not found');
        }

        $result = array();
        $row = 0;

        foreach($all_users as $value) {

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

    public function returnOneUser($id_user)
    {
        $actual_user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['id' => $id_user]);

        if(empty($actual_user)){

            throw new HttpException(204, 'User not found');
        }

        $result = array();

        $result['id'] = $actual_user->getId();
        $result['user_name'] = $actual_user->getUsername();
        $result['user_mail'] = $actual_user->getEmail();
        $result['user_role'] = $actual_user->getRoles();
        $result['user_active'] = $actual_user->getActive();
        $result['user_create_date'] = $actual_user->getUserCreateDate();
        $result['user_api'] = $actual_user->getApiKey();
        $result['user_photo'] = $actual_user->getPhoto();

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

    public function createNewSuperUser($content)
    {
        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $user = new User();

        $user->setUsername($user_data['username']);
        $user->setUserCreateDate(new \DateTime('now'));
        $user->setEmail($user_data['email']);
        $user->setActive($user_data['active']);
        $user->setRoles($user_data['role']);
        $user->setApiKey($user_data['api_key']);
        $user->setPassword($this->hashPassword($user, $user_data['password']));

        $this->validator($user);

        $this->getDoctrine()->getRepository('AppBundle:User')->saverObject($user);

        return 'User create!';
    }

    public function createNewUser($content)
    {
        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $user = new User();

        $user->setUsername($user_data['username']);
        $user->setUserCreateDate(new \DateTime('now'));
        $user->setEmail($user_data['email']);
        $user->setActive(0);
        $user->setRoles('ROLE_USER');
        $random_api = bin2hex(random_bytes(32));
        $user->setApiKey($random_api);
        $user->setPassword($this->hashPassword($user, $user_data['password']));

        $this->validator($user);

        $this->getDoctrine()->getRepository('AppBundle:User')->saverObject($user);

        $heading = 'Hi, friend!';
        $from = 'grandShushpanchik@gmail.com';
        $setTo = $user_data['email'];
        $text_message = 'Welcome! Link for activation your account:  ' .'http://'. $_SERVER['HTTP_HOST']. '/activation?apikey=' . $random_api;

        $this->get('my_mailer')->sendEmail($heading, $from, $setTo, $text_message);

        return 'New user create! Sent a letter to the email to activate. Email:  ' . $user_data['email'];
    }

    public function userActivation($apiKey)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $actual_user = $repo->findOneBy(['apiKey' => $apiKey]);

        $actual_user->userActivator();

        $repo->saverObject($actual_user);
    }

    public function editUser($content, $user_id)
    {
        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $username = null;
        $email = null;
        $active = null;
        $role = null;
        $apiKey = null;
        $password = null;

        if(isset($user_data['username']))
        {
            $username = $user_data['username'];
        }
        if(isset($user_data['email'])){

            $email = $user_data['email'];
        }
        if(isset($user_data['active'])){

            $active = $user_data['active'];
        }
        if(isset($user_data['role'])){

            $role = $user_data['role'];
        }
        if(isset($user_data['api_key'])){

            $apiKey = $user_data['api_key'];
        }
        if(isset($user_data['password'])){

            $password = $user_data['password'];
        }

        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $actual_user = $repo->find($user_id);

        if($username) {

            $actual_user->setUsername($username);
        }

            if($email) {

                $actual_user->setEmail($email);
            }

            if($active) {

                $actual_user->setActive($active);
            }

            if($role) {

                $actual_user->setRoles($role);
            }

            if($apiKey) {

                $actual_user->setApiKey($apiKey);
            }

            if($password) {

                $actual_user->setPassword($this->hashPassword($actual_user, $password));
            }

            $this->validator($actual_user);

            $repo->saverObject($actual_user);
        }

    public function userLogin($content)
    {
        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $actual_user = $repo->findOneBy(['username' => $user_data['username'], 'email' => $user_data['email']]);

        if(empty($actual_user)){

            throw new NotFoundHttpException;
        }

        if($this->container->get('security.password_encoder')->isPasswordValid($actual_user, $user_data['password'])){

            $new_apiKey = bin2hex(random_bytes(32));

            $actual_user->setApiKey($new_apiKey);

            $repo->saverObject($actual_user);

            return $new_apiKey;

        }else{

            return 'Password not valid!';
        }

    }

    public function getLimitOffsetUsers($limit, $offset)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->getLimitOffsetUser($limit, $offset);

        if(empty($users)){

            throw new HttpException(204, 'Users not found');
        }

        $result = array();
        $row = 0;

        foreach($users as $value) {

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

    public function userDelete($id_user)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $actual_user = $repo->find($id_user);

        if(empty($actual_user)){

            throw new HttpException(204, 'Users not found');
        }

        $repo->removeObject($actual_user);

        return 'User with ID '. $id_user .' deleted!';
    }

    public function getAllUserPosts()
    {
        $actual_user = $this->getUser()->getUsername();

        $posts = $actual_user->getPosts();

        if(empty($posts)){

            throw new HttpException(204, 'Posts not found');
        }

        $result = array();
        $row = 0;

        foreach($posts as $value){

            $result[$row]['id'] = $value->getId();
            $result[$row]['author_post'] = $value->getAuthorPost();
            $result[$row]['name_post'] = $value->getNamePost();
            $result[$row]['picture_post'] = $value->getPicturePost();
            $result[$row]['date_create_post'] = $value->getDateCreatePost();
            $result[$row]['text_post'] = $value->getTextPost();
            $result[$row]['id_author_post'] = $value->getUserPost()->getId();

            $row++;
        }

        return $result;
    }

    public function getAllUserComments()
    {
        $actual_user = $this->getUser()->getUsername();

        $comments = $actual_user->getComments();

        if(empty($comments)){

            throw new HttpException(204, 'Comments not found');
        }

        $result = array();
        $row = 0;

        foreach($comments as $value){

            $result[$row]['id'] = $value->getId();
            $result[$row]['author_comment'] = $value->getAuthorComment();
            $result[$row]['text_comment'] = $value->getTextComment();
            $result[$row]['date_create_comment'] = $value->getDateCreateComment();

            $row++;
        }

        return $result;
    }
}