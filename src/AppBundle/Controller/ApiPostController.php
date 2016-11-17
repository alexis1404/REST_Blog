<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiPostController extends Controller
{
    /**
     * @Route("/api/return_posts", name="return_post")
     * @Method("GET")
     */
    public function returnAllPostsAction()
    {
        return new JsonResponse($this->get('post_manager')->allPostReturn());
    }

    /**
     * @Route("/api/return_post/{id_post}", name="return_one_post")
     * @Method("GET")
     */
    public function getPostAccordingIdAction($id_post)
    {
        return new JsonResponse($this->get('post_manager')->onePostReturn($id_post));
    }

    /**
     * @Route("/api/return_posts_pagination/{limit}/{offset}", name="pagination_post")
     * @Method("GET")
     */
    public function getPostsPaginationAction($limit, $offset)
    {
        return new JsonResponse($this->get('post_manager')->postLimitOffset($limit, $offset));
    }

    /**
     * @Route("/api/post_delete/{id_post}", name="delete_post")
     * @Method("DELETE")
     */
    public function deletePostAction($id_post)
    {
        return new JsonResponse($this->get('post_manager')->postDelete($id_post));
    }

    /**
     * @Route("/api/create_post", name="create_post")
     * @Method("POST")
     */

    /*
     * Ожидает JSON-запрос в таком формате:
     *
     * {

    "post_name": "NamePost",
    "post_text": "Lorem ipsum dolor!"

    В результате будет создан новый пост. Его автор и дата создания будут установлены автоматически.
    Анонимный юзер не может создавать посты.
}
     */
    public function createPostAction(Request $request)
    {
        $content = $request->getContent();

        return new JsonResponse($this->get('post_manager')->createNewPost($content));
    }

    /**
     * @Route("/api/edit_post/{id_post}", name="edit_post")
     * @Method("PUT")
     */

    /*
     * Ожидает JSON-запрос в таком формате:
     *
     * {

    "name_post": "Edit Example",
    "text_post": "Lorem ipsum dolor! Is issum post!",
    "author_post": "Alex",
    "picture_post": "my_link_for_picture",
    "date_create_post": "10.10.2016",
    "text_post": "Bla-bla-bla..."
}
    Поля можно оставлять/добавлять в произвольном количестве/порядке. Можно выслать единственное
    поле, например:
    {
    "name_post": "Edit Example"
    }
     */
    public function editPostAction(Request $request, $id_post)
    {
        $content = $request->getContent();

        return new JsonResponse($this->get('post_manager')->editPost($content, $id_post));
    }

    /**
     * @Route("/api/all_comments_for_post/{id_post}", name="all_comments_post")
     * @Method("GET")
     */
    public function allCommentsForPostAction($id_post)
    {
        return new JsonResponse($this->get('post_manager')->getAllCommentsForThisPost($id_post));
    }

    /**
     * @Route("/api/upload_post_image/{id_post}", name="upload_post_image")
     * @Method("POST")
     */
    /*
     * Загружает сопроводительное изображение для поста. Если таковое
     * уже имеется, оно будет заменено на новое, а старая картинка будет удалена.
     */
    public function uploadPictureForPostAction(Request $request, $id_post)
    {
        $file = $request->files->get('image_post');

        return new JsonResponse($this->get('post_manager')->uploadPictureForPost($file, $id_post));
    }
}