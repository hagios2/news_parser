<?php

namespace App\Controller;

use App\Message\NewsParser;
use App\Repository\ArticleRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("articles")
     */
    public function index(ArticleRepository $repository, Request $request): Response
    {
        $queryBuilder = $repository->paginatorBuilder();

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('articles/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    /** @Route("/articles/{article_id}/delete", name="delete_article") */
    public function deleteProduct(int $article_id, ArticleRepository $repository): RedirectResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash(
                'error',
                'Permission Denied!'
            );

            return $this->redirectToRoute('app_article_index');
        }

        $article = $repository->find($article_id);

        $repository->remove($article, true);

        $this->addFlash(
            'success',
            'Article deleted successfully'
        );

        return $this->redirectToRoute('app_article_index');
    }

    /**
     * @Route("seed/articles")
     */
    public function seed(MessageBusInterface $bus)
    {
        $bus->dispatch(new NewsParser());

        return new Response('testing');
    }
}