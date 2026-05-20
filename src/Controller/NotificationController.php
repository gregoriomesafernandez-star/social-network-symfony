<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\NotificationService;

final class NotificationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/notifications', name: 'notificacions_page')]
    public function index(
        Request $request,
        NotificationService $notificationService,
        PaginatorInterface $paginator
    ): Response
    {
         /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $dql = "SELECT n FROM App\Entity\Notification n 
                WHERE n.user = :user 
                ORDER BY n.id DESC";

        $query = $this->entityManager
            ->createQuery($dql)
            ->setParameter('user', $user);

        $notifications = $paginator->paginate(
             $query,
             $request->query->getInt('page', 1),  
             5                                   
        );

        $notificationService->read($user);

        return $this->render('notification/notification_page.html.twig', [
            'user' => $user,
            'pagination' => $notifications
        ]);
    }

    #[Route('/notifications/get', name: 'notifications_get')]
    public function countNotificationsAction(
    ) : Response
    {

        $notifications_repo = $this->entityManager->getRepository(Notification::class);
        $notifications = $notifications_repo->findBy([
            'user' => $this->getUser(),
            'readed' => 'no'
        ]);

        return new Response(count($notifications));
    }
}
