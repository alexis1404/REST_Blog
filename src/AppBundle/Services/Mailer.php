<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Mailer extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendEmail($heading, $from, $to, $text_message)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($heading)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($text_message);

        $this->container->get('mailer')->send($message);
    }
}