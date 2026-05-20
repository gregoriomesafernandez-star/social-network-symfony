<?php

namespace App\Twig;

use App\Entity\Following;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FollowingExtension extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('is_following', [$this, 'isFollowing']),
        ];
    }

    /* Devuelve true si $follower sigue a $followed */
    public function isFollowing(User $follower, User $followed): bool
    {
        if (!$follower || !$followed) {
            return false;
        }

        $repo = $this->entityManager->getRepository(Following::class);

        $follow = $repo->findOneBy([
            'user' => $follower,
            'followed' => $followed
        ]);

        return $follow !== null;
    }
}