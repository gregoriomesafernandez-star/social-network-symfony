<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class GetUserExtension extends AbstractExtension
{

    public function __construct(
        private EntityManagerInterface $entityManager
        )
    {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('get_user', [$this, 'getUserFilter']),
        ];
    }

    public function getUserFilter(?int $user_id)
    {
        $users_repo = $this->entityManager->getRepository(User::class);
        $user = $users_repo->findOneBy([
            'id'=> $user_id 
        ]);

        if(!empty($user) && is_object($user)){
            $result = $user;
        }else{
            $result = false;
        }

        return $result;
        
    }
}
