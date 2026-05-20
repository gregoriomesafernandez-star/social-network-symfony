<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Like;
use App\Entity\Following;
use App\Entity\PrivateMessage;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        
        // 20 usuarios
        $users = [];
        $publications = [];
        $usedLikes = [];
        $usedFollows = [];
        
        for ($i = 1; $i <= 20; $i++) {

            $user = new User();

            $user->setName("User {$i}");
            $user->setSurname("Demo {$i}");
            $user->setEmail("user{$i}@test.com");
            $user->setNick("user{$i}");
            $user->setRole("ROLE_USER");
            $user->setBio("This is a demo user {$i}");
            $user->setActive("1");

            // imagen del usuario
            if (random_int(1, 100) <= 70) {
            $user->setImage("user".rand(1,10).".jpg");
            }else{
                $user->setImage("default-avatar.jpg");
            }

            // password
            $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
            $user->setPassword($hashedPassword);
            $users[] = $user;
            $manager->persist($user);

            // publicaciones
            for ($j = 1; $j <= 4; $j++) {

                $publication = new Publication();

                $publication->setUser($user);
                $publication->setText("Publication {$j} from user {$i}");

                // imagen aleatoria
                if (random_int(0,1)) {
                    $publication->setImage("demo-image-".rand(1,7).".jpg");
                }

                if (random_int(0, 1)) {
                    $publication->setDocument("demo-doc-1.pdf");
                }

                // estado y fecha
                $publication->setStatus("COMPLETED");
                $publication->setCreatedAt(new \DateTime());
                $publications[] = $publication;
                $manager->persist($publication);
                
            }
        }

        $manager->flush();


        // FOLLOWING
        $followingMap = [];

        foreach ($users as $user) {

            $followsCount = random_int(2, 5);

            for ($i = 1; $i <= $followsCount; $i++) {
                $followed = $users[array_rand($users)];

                if ($user === $followed) {
                    continue;
                }

                $key = spl_object_id($user) . '-' . spl_object_id($followed);

                if (isset($usedFollows[$key])) {
                    continue;
                }

                $following = new Following();
                $following->setUser($user);
                $following->setFollowed($followed);

                $usedFollows[$key] = true;
                $followingMap[spl_object_id($user)][] = $followed;

                $manager->persist($following);

                // NOTIFICATION FOLLOW
                $notification = new Notification();
                $notification->setUser($followed);
                $notification->setType('follow');
                $notification->setTypeId($user->getId());
                $notification->setExtra($user->getNick().' started following you');
                $notification->setReaded('no');

                $manager->persist($notification);
            }
        }


        // LIKES
        foreach ($users as $user) {

            if (!isset($followingMap[spl_object_id($user)])) {
                continue;
            }

            $followedUsers = $followingMap[spl_object_id($user)];

            foreach ($publications as $publication) {

                if (!in_array($publication->getUser(), $followedUsers, true)) {
                    continue;
                }

                if (random_int(1, 100) > 35) {
                    continue;
                }


                $key = spl_object_id($user) . '-' . spl_object_id($publication);

                if (isset($usedLikes[$key])) {
                    continue;
                }

                $like = new Like();
                $like->setUser($user);
                $like->setPublication($publication);

                $usedLikes[$key] = true;

                $manager->persist($like);

                // NOTIFICATION LIKE
                $notification = new Notification();
                $notification->setUser($publication->getUser());
                $notification->setType('like');
                $notification->setTypeId($user->getId());
                $notification->setExtra($user->getNick() . ' liked your publication');
                $notification->setReaded('no');

                $manager->persist($notification);
            }
        }


        // PRIVATE MESSAGES
        $messages = [
            'Hey, how are you?',
            'Nice publication!',
            'Thanks for following me.',
            'This demo app looks great.',
            'Hello! this is a test message.',
            'I really like your profile.',
        ];

        for ($i = 1; $i <= 50; $i++) {

            $emitter = $users[array_rand($users)];

            if (!isset($followingMap[spl_object_id($emitter)])) {
                continue;
            }

            $followedUsers = $followingMap[spl_object_id($emitter)];

            if (empty($followedUsers)) {
                continue;
            }

            $receiver = $followedUsers[array_rand($followedUsers)];

            if ($emitter === $receiver) {
                continue;
            }

            $privateMessage = new PrivateMessage();
            $privateMessage->setEmitter($emitter);
            $privateMessage->setReceiver($receiver);
            $privateMessage->setMessage($messages[array_rand($messages)]);
            
            if (random_int(0,1)) {
                $privateMessage->setImage("demo-image-".rand(1,7).".jpg");
            }

            if (random_int(0, 1)) {
                $privateMessage->setFile("demo-doc-1.pdf");
            }

            $privateMessage->setReaded(random_int(0, 1) ? 'yes' : 'no');
            $privateMessage->setCreatedAt(new \DateTime());

            $manager->persist($privateMessage);
        }

        $manager->flush();
    }
}