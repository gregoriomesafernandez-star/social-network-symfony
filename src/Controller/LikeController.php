<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Like;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Service\NotificationService;

final class LikeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    // ===== ME GUSTA =====
    #[Route('/like/{id}', name: 'app_like')]
    public function like(
        NotificationService $notificationService,
        int $id
    ): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $publications_repo = $this->entityManager->getRepository(Publication::class);
        $publication = $publications_repo->find($id);

        $like= new Like();
        $like->setUser($user);
        $like->setPublication($publication);

        $this->entityManager->persist($like);

        try {

            $this->entityManager->flush();

            if($publication->getUser() !== $user ){
                $notificationService->set($publication->getUser(), 'like', $user->getId(), $user->getNick() . ' liked publication: ' . $publication->getId());
            }
            

            $status = '¡Te gusta esta publicación!';
        } catch (\Throwable $e) {

            $status = 'No se ha podido guardar el me gusta';
        }

        return new Response($status);
    }

    // ===== NO ME GUSTA =====
    #[Route('/unlike/{id}', name: 'app_unlike')]
    public function unlike(
        int $id
    ): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $likes_repo =  $this->entityManager->getRepository(Like::class);
        $like = $likes_repo->findOneBy([
            'user' => $user,
            'publication' => $id 
        ]);

         $this->entityManager->remove($like);

        try {

            $this->entityManager->flush();

            $status = 'Ya no te gusta esta publicación';
        } catch (\Throwable $e) {

            $status = 'No se ha podido borrar el me gusta';
        }

        return new Response($status);
    }

    // ===== LIKES DE USUARIO =====
    #[Route('/likes/{nickname}', name: 'user_likes')]
    public function likesAction(
        Request $request,
        PaginatorInterface $paginator,
        string $nickname
    ): Response {

            $users_repo = $this->entityManager->getRepository(User::class);
            $user = $users_repo->findOneBy(["nick" => $nickname]);
        
        if(!$user){
         return $this->redirectToRoute('app_home');       
        }

        $dql = "SELECT l 
            FROM App\Entity\Like l 
            WHERE l.user = :user 
            ORDER BY l.id DESC";

        $query = $this->entityManager
            ->createQuery($dql)
            ->setParameter('user', $user);

        $likes = $paginator->paginate(
             $query,
             $request->query->getInt('page', 1),  
             5                                   
        );

        return $this->render('like/likes.html.twig', [
            'profile_user' => $user,
            'pagination'  => $likes
        ]);
    }
}
