<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\Following;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserStatesExtensionRuntime implements RuntimeExtensionInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function doSomething($value)
    {
        // ...
    }

    public function userStatsFilter(?User $user = null): array
    {
        $following_repo = $this->entityManager->getRepository(Following::class);
        $publications_repo = $this->entityManager->getRepository(Publication::class);
        $likes_repo = $this->entityManager->getRepository(Like::class);

        $user_following = $following_repo->findBy(['user' => $user]);
        $user_followers = $following_repo->findBy(['followed' => $user]);
        $user_publications = $publications_repo->findBy(['user' => $user]);
        $user_likes = $likes_repo->findBy(['user' => $user]);

        $result = [
            'following' => count($user_following),
            'followers' => count($user_followers),
            'publications' => count($user_publications),
            'likes' => count($user_likes)
        ];

        return $result;
    }
}
