<?php

namespace App\Twig\Extension;

use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LikedExtension extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('liked', [$this, 'isLiked']),
        ];
    }

    //?Publication $publication = null
    public function isLiked(
        User $user, 
        Publication $publication)
    : bool
    {
        if (!$user) {
            return false;
        }

        $likes_repo = $this->entityManager->getRepository(Like::class);

        $like = $likes_repo->findOneBy([
            'user'        => $user,
            'publication' => $publication
        ]);

        return $like !== null;
    }
}