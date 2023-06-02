<?php

namespace App\Controller;

use App\Entity\Follower;
use App\Form\UpdateAvatarFormType;
use App\Repository\FollowerRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FollowerRepository $followerRepository,
        private readonly PostRepository $postRepository,
        private readonly FileUploader $fileUploader
    ) {
    }

    #[Route('/', name: 'app_profile')]
    public function me(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $posts = $this->postRepository->findBy(['user' => $user]);

        return $this->render('profile/me.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    #[Route('/{id}', name: 'app_user')]
    public function index(int $id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if ($user) {
            if ($this->getUser() === $this->userRepository->findOneBy(['id' => $user->getId()])) {
                return $this->redirectToRoute('app_profile');
            }
        } else {
            throw new NotFoundHttpException();
        }

        $posts = $this->postRepository->findBy(['user' => $user]);
        $isFollowed = (bool)$this->followerRepository->findOneBy(['user' => $user, 'follower' => $this->getUser()]);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'isFollowed' => $isFollowed
        ]);
    }

    #[Route('/update-avatar', name: 'app_update_avatar')]
    public function changeAvatar(Request $request): Response
    {
        $user = $this->getUser();
        $oldAvatarPath = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()])->getAvatarPath();
        $form = $this->createForm(UpdateAvatarFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldAvatarPath) {
                $this->fileUploader->remove($this->getParameter('avatars_directory'), $oldAvatarPath);
            }

            $user = $form->getData();
            $avatar = $form->get('avatarPath')->getData();

            $fileName = $this->fileUploader->upload($this->getParameter('avatars_directory'), $avatar);

            $this->userRepository->updateAvatar($user, $fileName);

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/update_avatar.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/follow', name: 'app_follow')]
    #[IsGranted('ROLE_USER')]
    public function handleFollow(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $follow = $this->followerRepository->findOneBy(['user' => $user, 'follower' => $this->getUser()]);

        if ($request->isXmlHttpRequest()) {
            if (!$follow) {
                $newFollow = new Follower();
                $newFollow->setUser($user)->setFollower($this->getUser())->setCreatedAt(new \DateTimeImmutable());

                $this->followerRepository->save($newFollow, true);
            } else {
                $this->followerRepository->remove($follow, true);
            }
        }

        return $this->json(['isFollowed' => !$follow]);
    }
}
