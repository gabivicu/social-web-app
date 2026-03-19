<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Fetch a user with all relations needed for the profile page — avoids N+1:
     *  - userProfile
     *  - microPosts + their comments
     *  - comments written by the user
     *  - following list + their userProfile
     *  - followers list
     */
    public function findWithRelations(int $id): ?User
    {
        // First query: user + profile + posts + post comments
        $this->createQueryBuilder('u')
            ->leftJoin('u.userProfile', 'up')
            ->addSelect('up')
            ->leftJoin('u.microPosts', 'mp')
            ->addSelect('mp')
            ->leftJoin('mp.comments', 'mpc')
            ->addSelect('mpc')
            ->leftJoin('u.comments', 'uc')
            ->addSelect('uc')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        // Second query: user + following + their profiles (separate to avoid Cartesian product)
        return $this->createQueryBuilder('u')
            ->leftJoin('u.following', 'f')
            ->addSelect('f')
            ->leftJoin('f.userProfile', 'fp')
            ->addSelect('fp')
            ->leftJoin('u.followers', 'fl')
            ->addSelect('fl')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
