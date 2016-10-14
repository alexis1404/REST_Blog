<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Entity\Post;

class PostManager extends Controller
{
    protected $repo_post;

    protected $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->repo_post = $this->getDoctrine()->getRepository('AppBundle:Post');

        $this->em = $this->getDoctrine()->getManager();
    }

    public function createNewPost($content)
    {
        $actual_user = $this->getUser()->getUsername();

        $post_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $actual_user->createNewPost($post_data['post_name'], $post_data['post_text'], $actual_user);

        $this->em->flush();

        return 'Post create!';
    }

    public function allPostReturn()
    {
        $all_posts = $this->repo_post->findAll();

        if(empty($all_posts)){

            throw new HttpException(204, 'Posts not found');
        }

        $result = array();
        $row = 0;

        foreach($all_posts as $value){

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

    public function onePostReturn($id_post)
    {
        $post = $this->repo_post->find($id_post);

        if(empty($post)){

            throw new HttpException(204, 'Post not found');
        }

        $result = array();

        $result['id'] = $post->getId();
        $result['author_post'] = $post->getAuthorPost();
        $result['name_post'] = $post->getNamePost();
        $result['picture_post'] = $post->getPicturePost();
        $result['date_create_post'] = $post->getDateCreatePost();
        $result['text_post'] = $post->getTextPost();
        $result['id_author_post'] = $post->getUserPost()->getId();

        return $result;
    }

    public function postLimitOffset($limit, $offset)
    {
        $posts = $this->repo_post->getLimitOffsetPost($limit, $offset);

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

    public function postDelete($id_post)
    {
        $post = $this->repo_post->find($id_post);

        $id_post = $post->getId();

        $this->repo_post->removeObject($post);

        return 'Post with ID ' . $id_post . ' delete!';
    }

    public function editPost($content, $id_post)
    {
        $post_data = json_decode($content, true);

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $author_post = null;
        $name_post = null;
        $picture_post = null;
        $date_create_post = null;
        $text_post = null;

        if(isset($post_data['author_post']))
        {
            $author_post = $post_data['author_post'];
        }
        if(isset($post_data['name_post'])){

            $name_post = $post_data['name_post'];
        }
        if(isset($post_data['picture_post'])){

            $picture_post = $post_data['picture_post'];
        }
        if(isset($post_data['date_create_post'])){

            $date_create_post = $post_data['date_create_post'];
        }
        if(isset($post_data['text_post'])){

            $text_post = $post_data['text_post'];
        }

        $actual_post = $this->repo_post->find($id_post);

        if($author_post){

            $actual_post->setAuthorPost($author_post);
        }

        if($name_post){

            $actual_post->setNamePost($name_post);
        }

        if($picture_post){

            $actual_post->setPicturePost($picture_post);
        }

        if($date_create_post){

            $actual_post->setDateCreatePost(new \DateTime($date_create_post));
        }

        if($text_post){

            $actual_post->setTextPost($text_post);
        }

        $this->validator($actual_post);

        $this->repo_post->saverObject($actual_post);

        return 'Post edit successfully';
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
