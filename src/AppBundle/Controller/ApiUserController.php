<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiUserController extends Controller
{

    /**
     * @Route("/api/get_all_users", name="get_all_users")
     * @Method("GET")
     */
    public function getAllUsersAction()
    {
        return new Response(json_encode($this->get('user_manager')->returnManyUsers()));
    }

    /**
     * @Route("/api/get_user/{id_user}", name="get_user_id")
     * @Method("GET")
     */
    public function getUserAccordingIdAction($id_user)
    {
        return new Response(json_encode(($this->get('user_manager')->returnOneUser($id_user))));
    }

    /**
     * @Route("/api/get_users/{limit}/{offset}", name="user_pagination")
     * @Method("GET")
     */
    public function getUserPaginationAction($limit, $offset)
    {
        return new Response(json_encode($this->get('user_manager')->getLimitOffsetUsers($limit, $offset)));
    }

    /**
     * @Route("/api/delete_user/{id_user}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteUserAction($id_user)
    {
        return new Response(json_encode($this->get('user_manager')->userDelete($id_user)));
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

        return  new Response($this->get('user_manager')->createNewSuperUser($content));
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

        return  new Response($this->get('user_manager')->createNewUser($content));
    }

    /**
     * @Route("/activation", name="user_activate")
     * @Method("GET")
     */
    public function activationUserAction(Request $request)
    {
        $apikey = $request->query->get('apikey');

        $this->get('user_manager')->userActivation($apikey);

        return  new Response('User activate! Congratulation!', 200);
    }

    /**
     * @Route("/api/edit_user/{id_user}", name="user_edit")
     * @Method("PUT")
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
    Количество полей может быть произвольным (можно отправить, к примеру, только "username": "Shurik")
    */
    public function editUserAction($id_user, Request $request)
    {
        $content = $request->getContent();

        $this->get('user_manager')->editUser($content, $id_user);

        return  new Response('User ' . $id_user. ' successfully  changed!', 200);
    }

    /**
     * @Route("/login", name="login_user")
     * @Method("POST")
     */

    /*
     * Ожидает данные в таком виде:
     *
     * {

    "username": "Alexis",
    "email": "luceatlux@gmail.com",
    "password": "qwerty"

}

     *
     * В случае успешной авторизации возвращает НОВЫЙ Api Key для юзера
     */
    public function userLoginAction(Request $request)
    {
        $content = $request->getContent();

        return  new Response($this->get('user_manager')->userLogin($content));
    }

    /**
     * @Route("/api/logout", name="out_user")
     * @Method("GET")
     */
    public function logoutUserAction()
    {
        $actual_user = $this->getUser()->getUsername();

        $actual_user->logout();

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return  new Response($actual_user->getUsername() . ' logout success!', 200);
    }

    /**
     * @Route("/api/all_user_posts", name="all_user_posts")
     * @Method("GET")
     */
    public function allUserPostsAction()
    {
        return new Response(json_encode($this->get('user_manager')->getAllUserPosts()));
    }

    /**
     * @Route("/api/all_user_comments", name="all_user_comments")
     * @Method("GET")
     */
    public function allUserCommentsAction()
    {
        return new Response(json_encode($this->get('user_manager')->getAllUserComments()));
    }

    /**
     * @Route("/api/upload_avatar/{id_user}", name="upload_avatar")
     * @Method("POST")
     */
    /*
     * Загружает аватарку для юзера. Если у юзера уже есть аватар
     * он будет заменен на новый, а старый файл будет удален.
     */
    public function uploadAvatarAction(Request $request, $id_user)
    {
        $file = $request->files->get('user_avatar');

        return new Response($this->get('user_manager')->avatarUploader($file, $id_user));
    }
}