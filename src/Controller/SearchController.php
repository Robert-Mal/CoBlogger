<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    #[Route('/search', name: 'app_search')]
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('q');
        $results = $this->postRepository->findByTitle($search, 5);

        $realEntities = [];
        foreach ($results as $result) {
            $realEntities[$result->getId()] = $result->getTitle();
        }

        return $this->json($realEntities);
    }
}
