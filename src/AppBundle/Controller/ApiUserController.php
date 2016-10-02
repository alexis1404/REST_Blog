<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;

class ApiUserController extends Controller
{
    /**
     * @Route("/api/get_all_users", name="get_all_users")
     * @Method("GET")
     */
    public function getAllUsersAction()
    {
        $all_users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        if(empty($all_users)){

            throw new NotFoundHttpException;
        }

        $result = $this->get('user_manager')->returnManyUsers($all_users);

        $response = new Response(json_encode($result));

        return $response;
    }

    /**
     * @Route("/api/get_user/{id}", name="get_user_id")
     * @Method("GET")
     */
    public function getUserAccordingId($id)
    {
        $actual_user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['id' => $id]);

        if(empty($actual_user)){

            throw new NotFoundHttpException;
        }

        $result = $this->get('user_manager')->returnOneUser($actual_user);

        $response = new Response(json_encode($result));

        return $response;
    }

    /**
     * @Route("/api/get_users/{limit}/{offset}", name="user_pagination")
     * @Method("GET")
     */
    public function getUserPaginationAction($limit, $offset)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->getLimitOffsetUser($limit, $offset);

        if(empty($users)){

            throw new NotFoundHttpException;
        }

        $result = $this->get('user_manager')->returnManyUsers($users);

        $response = new Response(json_encode($result));

        return $response;
    }

    /**
     * @Route("/api/delete_user/{id_user}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteUserAction($id_user)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $actual_user = $repo->find($id_user);

        if(empty($actual_user)){

            throw new NotFoundHttpException;
        }

        $repo->removeObject($actual_user);

        return new Response('User with ID '. $id_user .' deleted!');
    }

    /**
     * @Route("/api/create_admin_user", name="user_admin_creator")
     * @Method("POST")
     */

    /*
    Ожидает JSON-данные в таком виде:
    {

    "username": "Shurik",
    "email": "shurik@gmail.com",
    "active": 1,
    "role": "ROLE_ADMIN",
    "api_key": "ekll@#0)llrfdvll232323245fffd",
    "password": "qwerty"

}
    В результате будет создан уже активный юзер с ролью ADMIN. Пароль шифруется при помощи bcrypt
    */
    public function createUserAbsoluteAction(Request $request)
    {
        $content = $request->getContent();
        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new \HttpRequestException;
        }

        $this->get('user_manager')->createNewSuperUser(
            $user_data['username'],
            $user_data['email'],
            $user_data['active'], // 1 - user active, any other - user inactive (boolean value)
            $user_data['role'],
            $user_data['api_key'],
            $user_data['password']
        );

        return  new Response('User create!', 200);
    }


    /**
     * @Route("/api/create_user", name="user_create")
     * @Method("POST")
     */

    /*
    Ожидает JSON-данные в таком виде:
    {

    "username": "Shurik",
    "email": "shurik@gmail.com",
    "password": "qwerty"

}
    В результате будет создан неактивный юзер с ролью USER. Пароль шифруется при помощи bcrypt, а на
    указанный email будет отправлено письмо для активации юзера.
    */
    public function createNewUserAction(Request $request)
    {
        $content = $request->getContent();

        $user_data = json_decode($content, true);

        if(empty($content)){

            throw new \HttpRequestException;
        }

        $this->get('user_manager')->createNewUser(
            $user_data['username'],
            $user_data['email'],
            $user_data['password']
        );

        return  new Response('User create!', 200);
    }

    /**
     * @Route("/activation", name="user_activate")
     * @Method("GET")
     */
    public function activationUserAction(Request $request)
    {
        $apikey = $request->query->get('apikey');


    }
}