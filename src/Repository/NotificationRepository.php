<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Notification[]
     */
    public function findByOwner(User $owner, int $limit = 20): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.actor', 'a')
            ->addSelect('a')
            ->leftJoin('a.userProfile', 'ap')
            ->addSelect('ap')
            ->where('n.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countUnseen(User $owner): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.owner = :owner')
            ->andWhere('n.seen = false')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllSeen(User $owner): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.seen', 'true')
            ->where('n.owner = :owner')
            ->andWhere('n.seen = false')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->execute();
    }
}
