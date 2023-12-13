<?php

namespace App\Controller;



// src/Controller/ArticleController.php

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/articles")
 */
class ArticleController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/active", name="article_active", methods={"GET"})
     */
    public function active(): JsonResponse
    {
        $articles = $this->entityManager->getRepository(Article::class)->findBy(['status' => true]);

        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'createdAt' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                'publishedAt' => $article->getPublishedAt() ? $article->getPublishedAt()->format('Y-m-d H:i:s') : null,
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'status' => $article->getStatus(),
            ];
        }

        return $this->json(['active_articles' => $data]);
    }

    /**
     * @Route("/add", name="article_add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $article = new Article();
        $article->setCreatedAt(new \DateTime());
        $article->setPublishedAt(new \DateTime($data['publishedAt']));
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setStatus($data['status']);

        $entityManager = $this->entityManager;
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json(['message' => 'Article added successfully']);
    }
}
