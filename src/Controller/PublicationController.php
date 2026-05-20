<?php

namespace App\Controller;

use App\Entity\Following;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\PublicationType;
use Knp\Component\Pager\PaginatorInterface;

class PublicationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator
    ) {}

    //HOME
    #[Route('/home', name: 'app_home')]
    public function index(
        Request $request
    ): Response
    {
        $user = $this->getUser();
        $publication = new Publication();                                     
        $form = $this->createForm(PublicationType::class, $publication); 

        $form->handleRequest($request);

        if($form->isSubmitted()){

            if($form->isValid()){ 

                //upload image
                $file = $form['image']->getData();

                if($file){

                    $ext = $file->guessExtension() ?? $file->getClientOriginalExtension();

                    if($ext == 'jpeg') {
                        $ext = 'jpg';
                    }

                    $allowed = ['jpg', 'png', 'gif'];

                    if (in_array($ext, $allowed)) {

                        /** @var \App\Entity\User $user */
                        $user = $this->getUser();

                        $file_name = $user->getId() . '_' . time() . '.' . $ext;

                        $file->move("uploads/publications/images", $file_name);

                        $publication->setImage($file_name);

                    }else{
                        $publication->setImage(null);
                    }

                }else{
                    $publication->setImage(null);
                }

                //upload document
                $doc = $form['document']->getData();

                if($doc){

                    $ext = $doc->guessExtension();
                    
                    if($ext == 'pdf'){
                        
                        /** @var \App\Entity\User $user */
                        $file_name = $user->getId().time() .".". $ext;
                        
                        $doc->move("uploads/publications/documents", $file_name);
                        
                        $publication->setDocument($file_name);
                    }else{
                        $publication->setDocument(null);
                    }   

                }else{
                    $publication->setDocument(null);
                }

                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime("now"));
                $publication->setStatus("COMPLETED");
                $this->entityManager->persist($publication);

                try {
                    
                    $this->entityManager->flush();

                    $status = 'La publicación se ha creado correctamente';
                    $this->addFlash('success', $status);

                } catch (\Exception $e) {

                    $status = 'Error al añadir publicación';
                    $this->addFlash('danger', $status);
                }
                    
                return $this->redirectToRoute('app_home');
            
            }else{

                $status = "La publicacion no se ha creado correctamente, formulario no válido";
                $this->addFlash('danger', $status);
            }
        }

        $publications = $this->getPublications($request);

        return $this->render('publication/home.html.twig', [
           'form' => $form->createView(),
           'pagination' => $publications

        ]);
    }

    //FUNCION PARA MIS PUBLICACIONES Y LAS DE LOS QUE SIGO
    public function getPublications(
        Request $request
    )
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $publications_repo = $this->entityManager->getRepository(Publication::class);
        $following_repo = $this->entityManager->getRepository(Following::class);

        $following = $following_repo->findBy(['user' => $user]);

        $following_array = [];

        foreach($following as $follow){
            $following_array[] = $follow->getFollowed();
        }

        $query = $publications_repo->createQueryBuilder('p')
        ->where('p.user = :user')
        ->setParameter('user', $user)
        ->orderBy('p.id', 'DESC');

        if(count($following_array) > 0){
            $query->orWhere('p.user IN (:following)')
                   ->setParameter('following', $following_array);
        }

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),  
            5                                   
        );

        return $pagination;
    }

    //FUNCION PARA BORRAR MIS PUBLICACIONES POR ID
    #[Route('/publication/remove/{id}', name: 'remove_publication')]
    public function removePublication(
        int $id
    )
    {

        $publications_repo = $this->entityManager->getRepository(Publication::class);
        $publication = $publications_repo->find($id);

        if (!$publication) {
            return new Response('Publicación no encontrada');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        
        if($user->getId() != $publication->getUser()->getId()){

           return new Response('No tienes permiso para borrar esta publicación');
        }

        try {
            $this->entityManager->remove($publication);
            $this->entityManager->flush();

            return new Response('La publicación se ha borrado correctamente');

        } catch (\Exception $e) {
            return new Response('Error al borrar la publicación');
        }
    }

}