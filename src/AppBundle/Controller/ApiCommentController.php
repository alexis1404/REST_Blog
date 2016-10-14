<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiCommentController extends Controller
{
    /**
     * @Route("/api/get_all_comments", name="get_all_comments")
     * @Method("GET")
     */
    public function getAllCommentsAction()
    {
        return new Response(json_encode($this->get('comment_manager')->getAllComments()));
    }

    /**
     * @Route("/api/get_comment/{id_comment}", name="get_comment")
     * @Method("GET")
     */
    public function getCommentAccordingIdAction($id_comment)
    {
        return new Response(json_encode($this->get('comment_manager')->getCommentAccordingId($id_comment)));
    }

    /**
     * @Route("/api/get_comments_pagination/{limit}/{offset}", name="pagination_comments")
     * @Method("GET")
     */
    public function getCommentPaginationAction($limit, $offset)
    {
        return new Response(json_encode($this->get('comment_manager')->getCommentsLimitOffset($limit, $offset)));
    }

    /**
     * @Route("/api/delete_comment/{id_comment}", name="delete_comment")
     * @Method("DELETE")
     */
    public function deleteCommentAction($id_comment)
    {
        return new Response($this->get('comment_manager')->deleteComment($id_comment));
    }

    /**
     * @Route("/api/create_comment", name="create_comment")
     * @Method("POST")
     */

    /*
     * Ожидает JSON-данные в таком формате:
     *
     * {

    "id_post": 8, - ID поста к которому оставлен коментарий
    "text_comment": "Это тестовый коментарий № 2" - текст коментария
      }
    В результате будет создан новый коментарий, связанный с постом с указанным ID, текущим юзером и
    автоматически указанным юзернеймом и датой. Анонимные юзеры не могут оставлять коментарии.
     */
    public function createCommentAction(Request $request)
    {
        $content = $request->getContent();

        return new Response($this->get('comment_manager')->createComment($content));
    }

    /**
     * @Route("/api/edit_comment", name="edit_comment")
     * @Method("PUT")
     */
    public function editCommentAction(Request $request)
    {
        $content = $request->getContent();

        return new Response($this->get('comment_manager')->editComment($content));
    }
}