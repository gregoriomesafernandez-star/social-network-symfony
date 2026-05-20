<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Following;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\NotificationService;

class FollowingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    // ===== SEGUIR USUARIO =====
    #[Route('/follow', name: 'following_follow', methods: ['POST'])]
    public function followAction(
        Request $request,
        NotificationService $notificationService
    ): Response {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $followed_id = $request->get('followed');

        
        $user_repo = $this->entityManager->getRepository(User::class);
        $followed = $user_repo->find($followed_id);

        $following = new Following();
        $following->setUser($user);
        $following->setFollowed($followed);

        $this->entityManager->persist($following);

        try {

            $this->entityManager->flush();

            $notificationService->set($followed, 'follow', $user->getId(), $user->getNick() . ' started following user: ' . $followed->getId());
            $status = "Ahora estás siguiendo a este usuario";
        } catch (\Throwable $e) {

            $status = "No se ha podido seguir a este usuario";
        }

        return new Response($status);
    }

    // ===== DEJAR DE SEGUIR USUARIO =====
    #[Route('/unfollow', name: 'following_unfollow', methods: ['POST'])]
    public function unfollowAction(
        Request $request
    ): Response {

        $user = $this->getUser();
        $followed_id = $request->get('followed');

        
        $following_repo = $this->entityManager->getRepository(Following::class);
        $followed = $following_repo->findOneBy(array(
            'user' => $user,
            'followed' => $followed_id
        ));

        $this->entityManager->remove($followed);
       
        try {

            $this->entityManager->flush();

            $status = "Has dejado de seguir a este usuario";
        } catch (\Throwable $e) {

            $status = "No se ha podido dejar de seguir a este usuario";
        }

        return new Response($status);
    }

    // ===== GENTE QUE USUARIO ESTÁ SIGUENDO  =====
    #[Route('/following/{nickname}', name: 'following_users')]
    public function followingAction(
        Request $request,
        string $nickname
    ): Response {

        $users_repo = $this->entityManager->getRepository(User::class);
        $user = $users_repo->findOneBy(["nick" => $nickname]);

        if(!$user){
         return $this->redirectToRoute('app_home');       
        }

        $dql = "SELECT f 
            FROM App\Entity\Following f 
            WHERE f.user = :user 
            ORDER BY f.id DESC";

        $query = $this->entityManager
            ->createQuery($dql)
            ->setParameter('user', $user);

        $following = $this->paginator->paginate(
             $query,
             $request->query->getInt('page', 1),  
             5                                   
        );

        return $this->render('following/following.html.twig', [
            'type' => 'following',
            'profile_user' => $user,
            'pagination'  => $following
        ]);
    }

    // ===== SEGUIDORES DE USUARIO =====
    #[Route('/followed/{nickname}', name: 'followed_users')]
    public function followedAction(
        Request $request,
        string $nickname
    ): Response {

        $users_repo = $this->entityManager->getRepository(User::class);
        $user = $users_repo->findOneBy(["nick" => $nickname]);
        
        if(!$user){
         return $this->redirectToRoute('app_home');       
        }

        $dql = "SELECT f 
            FROM App\Entity\Following f 
            WHERE f.followed = :user 
            ORDER BY f.id DESC";

        $query = $this->entityManager
            ->createQuery($dql)
            ->setParameter('user', $user);

        $followed = $this->paginator->paginate(
             $query,
             $request->query->getInt('page', 1),  // página actual
             5                                   // items por página
        );

        return $this->render('following/following.html.twig', [
            'type' => 'followed',
            'profile_user' => $user,
            'pagination'  => $followed
        ]);
    }
    
}