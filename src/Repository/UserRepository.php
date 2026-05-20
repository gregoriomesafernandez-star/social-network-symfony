<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Following;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, User::class);
    }

    public function getFollowingUsers(User $user){

        $following_repo = $this->entityManager->getRepository(Following::class);

        $following = $following_repo->findBy(['user' => $user]);

        $following_array = [];

        foreach($following as $follow){
            $following_array[] = $follow->getFollowed();
        }

        $users_repo = $this->entityManager->getRepository(User::class);
        $users = $users_repo->createQueryBuilder('u')
                ->where("u.id != :user AND u.id IN (:following)")
                ->setParameter('user', $user->getId())
                ->setParameter('following', $following_array)
                ->orderBy('u.id', 'DESC');
        
        return $users;
    }
}
