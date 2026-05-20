<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PrivateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\PrivateMessageType;

final class PrivateMessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    #[Route('/private-message', name: 'app_private_message_index')]
    public function index(
        Request $request
    ): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $private_message = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $private_message, [
            'user' => $user,
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {

            if ($form->isValid()) {

                //upload image
                $file = $form['image']->getData();

                if ($file) {

                    $ext = $file->guessExtension() ?? $file->getClientOriginalExtension();

                    if ($ext == 'jpeg') {
                        $ext = 'jpg';
                    }

                    $allowed = ['jpg', 'png', 'gif'];

                    if (in_array($ext, $allowed)) {

                        $file_name = $user->getId() . '_' . time() . '.' . $ext;

                        $file->move("uploads/message/images", $file_name);

                        $private_message->setImage($file_name);
                    } else {
                        $private_message->setImage(null);
                    }
                } else {
                    $private_message->setImage(null);
                }

                //upload document
                $doc = $form['file']->getData();

                if ($doc) {

                    $ext = $doc->guessExtension();

                    if ($ext == 'pdf') {

                        /** @var \App\Entity\User $user */
                        $file_name = $user->getId() . time() . "." . $ext;

                        $doc->move("uploads/message/documents", $file_name);

                        $private_message->setFile($file_name);
                    } else {
                        $private_message->setFile(null);
                    }
                } else {
                    $private_message->setFile(null);
                }

                $private_message->setEmitter($user);
                $private_message->setCreatedAt(new \DateTime("now"));
                $private_message->setReaded('no');

                $this->entityManager->persist($private_message);

                try {

                    $this->entityManager->flush();
                    $this->addFlash('success', 'Mensaje enviado correctamente');
                } catch (\Exception $e) {

                    $this->addFlash('danger', 'Error al enviar el mensaje');
                }

                return $this->redirectToRoute('app_private_message_index');
            }else{
                $this->addFlash('danger', 'El mensaje privado no se ha enviado, error en formulario');
            }
        }

        $private_messages = $this->getPrivateMessages($request, null);
        $this->setReaded($user);

        return $this->render('private_message/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $private_messages
        ]);
    }

    #[Route('/private-message/sended', name: 'private_message_sended')]
    public function sended(
        Request $request
    ): Response
    {
        $private_messages = $this->getPrivateMessages($request, "sended");
        $this->setReaded($this->getUser());
        return $this->render('private_message/sended.html.twig', [
            'pagination' => $private_messages
        ]);
    }

    public function getPrivateMessages(Request $request, ?string $type = null){
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $user_id = $user->getId();

        if($type == "sended"){
            $dql = "SELECT p FROM App\Entity\PrivateMessage p WHERE p.emitter = $user_id ORDER BY p.id DESC";
        }else{
            $dql = "SELECT p FROM App\Entity\PrivateMessage p WHERE p.receiver = $user_id ORDER BY p.id DESC";
        }

        $query = $this->entityManager->createQuery($dql);

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),  
            4                                   
        );


        return $pagination;
    }

    #[Route('/private-message/notification/get', name: 'private_message_notification_get')]
    public function noReaded(      
    ): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $private_messages_repo = $this->entityManager->getRepository(PrivateMessage::class);
        $count_not_readed_msg = $private_messages_repo->findBy([
            'receiver' => $user,
            'readed' => 'no'
        ]);

        return new Response(count($count_not_readed_msg));
    }

    public function setReaded(User $user){

        $private_messages_repo = $this->entityManager->getRepository(PrivateMessage::class);

        $messages = $private_messages_repo->findBy([
            'receiver' => $user,
            'readed' => 'no'
        ]);

        foreach ($messages as $msg) {
            $msg->setReaded('yes');
            $this->entityManager->persist($msg);
        }

        try {

            $this->entityManager->flush();
            $result = true;
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
}
