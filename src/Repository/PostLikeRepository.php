<?php

namespace App\Repository;

use App\Entity\PostLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostLike>
 *
 * @method PostLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostLike[]    findAll()
 * @method PostLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostLike::class);
    }

    public function save(PostLike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PostLike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $maxResults
     * @return PostLike[]
     */
    public function getTrendingPosts(int $maxResults): array
    {
        $dateTimeWeekAgo = new \DateTime();
        $dateTimeWeekAgo = $dateTimeWeekAgo->sub(new \DateInterval('P7D'))->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('p')
            ->select('IDENTITY(p.post), COUNT(p.post)')
            ->where('p.createdAt > :weekAgo')
            ->setParameter('weekAgo', $dateTimeWeekAgo)
            ->groupBy('p.post')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }
}
