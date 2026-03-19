<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * Fetch all posts with comments and authors — avoids N+1 on the index page.
     *
     * @return MicroPost[]
     */
    public function findAllWithComments(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch a single post with all relations needed for the show page:
     * author, comments and each comment's author — avoids N+1.
     */
    public function findWithAllRelations(int $id): ?MicroPost
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->leftJoin('c.author', 'ca')
            ->addSelect('ca')
            ->leftJoin('a.userProfile', 'ap')
            ->addSelect('ap')
            ->leftJoin('ca.userProfile', 'cap')
            ->addSelect('cap')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Fetch posts authored by users that a given user follows,
     * ordered by newest first. Eager-loads author + comments to avoid N+1.
     *
     * @return MicroPost[]
     */
    public function findByFollowedUsers(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->innerJoin('a.followers', 'follower')
            ->where('follower.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.created', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
