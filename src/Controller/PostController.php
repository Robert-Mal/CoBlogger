<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Form\PostFormType;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/post')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PostRepository $postRepository,
        private readonly PostLikeRepository $postLikeRepository,
        private readonly FileUploader $fileUploader
    ) {
    }

    #[Route('/{id}', name: 'post_read', priority: -10)]
    public function show(int $id): Response
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);
        $postLikes = count($this->postLikeRepository->findBy(['post' => $id]));
        $estimatedReadTime = $this->estimateReadTime($post->getBody());

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'postLikes' => $postLikes,
            'estimatedReadTime' => $estimatedReadTime
        ]);
    }

    #[Route('/create', name: 'post_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();

            $image = $form->get('imagePath')->getData();
            if ($image) {
                $fileName = $this->fileUploader->upload($this->getParameter('uploads_directory'), $image);
                $newPost->setImagePath('/uploads/' . $fileName);
            }

            $newPost->setUser($this->getUser());

            $this->entityManager->persist($newPost);
            $this->entityManager->flush();

            $this->addFlash('success', 'Post added');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('post/form.html.twig', [
            'pageName' => 'Create Post',
            'form' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'post_edit')]
    public function edit(int $id, Request $request): Response
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('POST_EDIT', $post);

        $oldImagePath = $post->getImagePath();
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('imagePath')->getData();

            if ($image) {
                if ($oldImagePath) {
                    $this->fileUploader->remove($this->getParameter('uploads_directory'), $oldImagePath);
                }

                $fileName = $this->fileUploader->upload($this->getParameter('uploads_directory'), $image);
                $post->setImagePath('/uploads/' . $fileName);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('post/form.html.twig', [
            'pageName' => 'Edit Post',
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'post_delete')]
    public function delete(int $id): Response
    {
        $post = $this->postRepository->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        if ($post->getImagePath()) {
            $this->fileUploader->remove($this->getParameter('uploads_directory'), $post->getImagePath());
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->addFlash('success', 'Post deleted');

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/like/{id}', name: 'post_like')]
    #[IsGranted('ROLE_USER')]
    public function handleLike(Request $request, int $id): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $post = $this->postRepository->findOneBy(['id' => $id]);
            $user = $this->getUser();

            $postLike = $this->postLikeRepository->findOneBy(['post' => $id, 'user' => $user]);
            if (!$postLike) {
                $postLike = new PostLike();
                $postLike->setPost($post)->setUser($user)->setCreatedAt(new \DateTimeImmutable());

                $this->postLikeRepository->save($postLike, true);
            } else {
                $this->postLikeRepository->remove($postLike, true);
            }
        }

        return $this->json(['likeCount' => count($this->postLikeRepository->findBy(['post' => $id]))]);
    }

    private function estimateReadTime(string $postBody): float
    {
        $totalWords = str_word_count($postBody);

        return ceil($totalWords / 200);
    }
}
