<?php

namespace App\Controller;

use App\Message\NewsParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("articles")
     */
    public function index(MessageBusInterface $bus)
    {
        $bus->dispatch(new NewsParser());

        return new Response('testing');
    }


}