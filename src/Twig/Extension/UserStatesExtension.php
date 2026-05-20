<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\UserStatesExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class UserStatesExtension extends AbstractExtension
{

    public function __construct()
    {
        
    }

    public function getFilters(): array
    {
        return [

            new TwigFilter('user_stats', [UserStatesExtensionRuntime::class, 'userStatsFilter']),
        ];
    }

    
}
