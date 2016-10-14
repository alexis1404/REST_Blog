<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Entity\Comment;

class CommentManager extends Controller
{
    protected $repo_comment;
    protected $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->repo_comment = $this->getDoctrine()->getRepository('AppBundle:Comment');

        $this->em = $this->getDoctrine()->getManager();
    }

    public function getAllComments()
    {
        $all_comments = $this->repo_comment->findAll();

        if(empty($all_comments)){

            throw new HttpException(204, 'Comments not found');
        }

        $result = array();
        $row = 0;

        foreach($all_comments as $value){

            $result[$row]['id'] = $value->getId();
            $result[$row]['author_comment'] = $value->getAuthorComment();
            $result[$row]['text_comment'] = $value->getTextComment();
            $result[$row]['date_create_comment'] = $value->getDateCreateComment();

            $row++;
        }

        return $result;
    }

    public function getCommentAccordingId($id_comment)
    {
        $actual_comment = $this->repo_comment->find($id_comment);

        if(empty($actual_comment)){

            throw new HttpException(204, 'Comment not found');
        }

        $result = array();

        $result['id'] = $actual_comment->getId();
        $result['author_comment'] = $actual_comment->getAuthorComment();
        $result['text_comment'] = $actual_comment->getTextComment();
        $result['date_create_comment'] = $actual_comment->getDateCreateComment();

        return $result;
    }

    public function getCommentsLimitOffset($limit, $offset)
    {
        $comments = $this->repo_comment->getLimitOffsetComments($limit, $offset);

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

    public function deleteComment($id_comment)
    {
        $actual_comment = $this->repo_comment->find($id_comment);

        if(empty($actual_comment)){

            throw new HttpException(204, 'Comment not found!');
        }

        $this->repo_comment->removeObject($actual_comment);

        return 'Comment with ID ' . $id_comment . ' successful delete!';
    }

    public function createComment($content)
    {
        $actual_user = $this->getUser()->getUsername();

        $comment_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $actual_post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($comment_data['id_post']);

        if(empty($actual_post)){

            throw new HttpException(204, 'Post not found!');
        }

        $data_for_validate = $actual_user->createNewComment($comment_data['text_comment'], $actual_user, $actual_post);

        $this->validator($data_for_validate);

        $this->em->flush();

        return 'Comment successful created!';
    }

    public function editComment($content)
    {
        $comment_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $actual_comment = $this->repo_comment->find($comment_data['id_comment']);

        if(empty($actual_comment)){

            throw new HttpException(204, 'Comment not found!');
        }

        $text_comment = null;
        $date_create_comment = null;

        if(isset($comment_data['text_comment']))
        {
            $text_comment = $comment_data['text_comment'];
        }

        if(isset($comment_data['date_create_comment']))
        {
            $date_create_comment = $comment_data['date_create_comment'];
        }

        if($text_comment){

            $actual_comment->setTextComment($text_comment);
        }

        if($date_create_comment){

            $actual_comment->setDateCreateComment(new \DateTime($date_create_comment));
        }

        $this->validator($actual_comment);

        $this->repo_comment->saverObject($actual_comment);

        return 'Comment with ID ' . $comment_data['id_comment'] . ' successful edit!';
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
}