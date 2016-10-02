<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiPostController extends Controller
{
    /**
     * @Route("/api/create_post", name="create_post")
     * @Method("POST")
     */
    public function createPostAction(Request $request)
    {
        $content = $request->getContent();
        $post_data = json_decode($content, true);

        if(empty($content)){

            throw new \HttpRequestException;
        }

        $actual_user = $this->getUser()->getUsername();

        $actual_user->createNewPost($post_data['post_name'], $post_data['post_text'], $actual_user);

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return new Response($post_data['post_name'], 200);
    }
}