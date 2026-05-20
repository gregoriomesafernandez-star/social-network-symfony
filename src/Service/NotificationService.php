<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
use App\Entity\User;


class NotificationService
{
    
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    // Aquí pones tus métodos
    public function set(
        User $user,
        string $type,
        int $typeId,
        ?string $extra
    ){
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTypeId($typeId);
        $notification->setReaded('no');
        $notification->setCreatedAt(new \DateTime("now"));
        $notification->setExtra($extra);

        $this->entityManager->persist($notification);

        try {
            $this->entityManager->flush();
            $status = true;
        } catch (\Throwable $e) {

            $status = false;
        }

        return $status;
    }

    public function read(User $user){

        $notifications_repo = $this->entityManager->getRepository(Notification::class);

        $notifications = $notifications_repo->findBy(['user' => $user]);

        foreach ($notifications as $notification) {
            $notification->setReaded('yes');
            $this->entityManager->persist($notification);
        }

        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}