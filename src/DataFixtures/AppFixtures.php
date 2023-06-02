<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Follower;
use App\Entity\Post;
use App\Entity\PostLike;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private readonly string                      $avatarsDirectory,
        private readonly string                      $uploadsDirectory,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UserRepository              $userRepository,
        private readonly CategoryRepository          $categoryRepository,
        private readonly PostRepository              $postRepository
    )
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new FakerPicsumImagesProvider($this->faker));
    }

    public function load(ObjectManager $manager): void
    {
        $this->addUsers($manager);
        $this->addCategories($manager);
        $this->addPosts($manager);
        $this->addFollowers($manager);
        $this->addPostLikes($manager);
    }

    private function addUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('test' . $i . '@example.com');
            $user->setRoles([]);
            $user->setPassword($this->hasher->hashPassword($user, 'Test1234'));
            $user->setIsVerified(true);

            $fileName = $this->faker->image($this->avatarsDirectory, 640, 640);
            $fileName = substr($fileName, strpos($fileName, '/avatars'));
            $fileName = str_replace('\\', '/', $fileName);
            $user->setAvatarPath(substr($fileName, strpos($fileName, '/public')));

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function addCategories(ObjectManager $manager): void
    {
        $categories = ['news', 'sports', 'technology', 'health', 'entertainment', 'finance', 'music', 'art', 'science', 'food'];
        for ($i = 0; $i < count($categories); $i++) {
            $category = new Category();
            $category->setName($categories[array_rand($categories)]);

            $manager->persist($category);
        }

        $manager->flush();
    }

    private function addPosts(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setUser($users[array_rand($users)]);
            $post->setTitle($this->faker->sentence());

            $fileName = $this->faker->image($this->uploadsDirectory, 640, 640);
            $fileName = substr($fileName, strpos($fileName, '/uploads'));
            $fileName = str_replace('\\', '/', $fileName);
            $post->setImagePath(substr($fileName, strpos($fileName, '/public')));

            $post->setDescription($this->faker->paragraph(4));
            $post->setCategory($categories[array_rand($categories)]);
            $post->setBody($this->faker->paragraphs(50, true));

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function addFollowers(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < 40; $i++) {
            do {
                $randomUserId = array_rand($users);
                $randomFollowerId = array_rand($users);
            } while ($randomUserId === $randomFollowerId);
            $ids[] = [$users[$randomUserId], $users[$randomFollowerId]];
        }
        $ids = array_unique($ids, SORT_REGULAR);

        foreach ($ids as $id) {
            $follower = new Follower();
            $follower->setUser($id[0]);
            $follower->setFollower($id[1]);
            $follower->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($follower);
        }

        $manager->flush();
    }

    private function addPostLikes(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $posts = $this->postRepository->findAll();

        for ($i = 0; $i < 50; $i++) {
            $postLikes[] = [$users[array_rand($users)], $posts[array_rand($posts)]];
        }
        $postLikes = array_unique($postLikes, SORT_REGULAR);

        foreach ($postLikes as $like) {
            $postLike = new PostLike();
            $postLike->setUser($like[0]);
            $postLike->setPost($like[1]);
            $postLike->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($postLike);
        }

        $manager->flush();
    }
}
