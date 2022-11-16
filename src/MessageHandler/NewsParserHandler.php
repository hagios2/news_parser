<?php

namespace App\MessageHandler;

use App\Entity\Article;
use App\Message\NewsParser;
use App\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NewsParserHandler implements MessageHandlerInterface
{
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private ArticleRepository $articleRepository;

    public function __construct(
        HttpClientInterface   $client,
        ParameterBagInterface $parameterBag,
        ArticleRepository     $articleRepository
    )
    {
        $this->client = $client;
        $this->parameterBag = $parameterBag;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function __invoke(NewsParser $newsParser)
    {
        $newsApiKey = $this->parameterBag->get('news_api_key');
        $url = "https://newsapi.org/v2/top-headlines?sources=techcrunch&apiKey={$newsApiKey}";

        try {
            $response = $this->client->request('GET', $url);
            $this->parseNews($response->toArray(), $this->articleRepository);
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    public function parseNews(array $newContents, ArticleRepository $articleRepository)
    {
        foreach ($newContents['articles'] as $news) {
            $existingArticle = $articleRepository->findOneBy(['title' => $news['title']]);

            if ($existingArticle) {
                $existingArticle->setTitle($news['title']);
                $existingArticle->setDescription($news['description']);
                $existingArticle->setPicture($news['urlToImage']);
                $existingArticle->setNote('Updated on ' . date('F jS Y \\a\\t g:ia'));
                $articleRepository->save($existingArticle, true);
            } else {
                $article = new Article();
                $article->setTitle($news['title']);
                $article->setDescription($news['description']);
                $article->setPicture($news['urlToImage']);

                $articleRepository->save($article, true);
            }
        }
    }
}