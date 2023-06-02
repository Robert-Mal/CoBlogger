<?php

namespace App\Controller;

use App\Repository\FollowerRepository;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly FollowerRepository $followerRepository,
        private readonly UserRepository $userRepository,
        private readonly PostRepository $postRepository,
        private readonly PostLikeRepository $postLikeRepository
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'followers' => $this->getFollowers(),
            'userPosts' => $this->getUserPosts(),
            'likedPosts' => $this->getLikedPosts(),
            'trendingPosts' => $this->getTrendingPosts()
        ]);
    }

    private function getFollowers(): ?array
    {
        if ($this->getUser()) {
            $userId = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()])->getId();
            return array_map(function ($follower) {
                return $this->userRepository->findOneBy(['id' => $follower[1]]);
            }, $this->followerRepository->getFollowers($userId));
        }

        return null;
    }

    private function getUserPosts(): array
    {
        return $this->postRepository->findBy(['user' => $this->getUser()]);
    }

    private function getLikedPosts(): array
    {
        return array_map(function ($postLike) {
            return $postLike->getPost();
        }, $this->postLikeRepository->findBy(['user' => $this->getUser()]));
    }

    private function getTrendingPosts(): array
    {
        return array_map(function ($post) {
            return $this->postRepository->findOneBy(['id' => $post[1]]);
        }, $this->postLikeRepository->getTrendingPosts(10));
    }
}
