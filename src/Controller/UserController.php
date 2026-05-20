<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {}
    
    // ===== LOGIN =====
    #[Route('/login', name: 'app_login')]
    public function loginAction(
        AuthenticationUtils $authenticationUtils
    ): Response {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Obtener el error de login (si existe)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Obtener el último username introducido (para rellenar el campo)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // ===== LOGOUT =====
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // ===== REGISTRO USUARIO =====
    #[Route('/register', name: 'app_register')]
    public function registerAction(
        Request $request
    ): Response {

        // Si usuario ya está logueado, redirigirlo
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();                                     
        $form = $this->createForm(RegistrationFormType::class, $user);   

        $form->handleRequest($request);                         

        if($form->isSubmitted()){

            if($form->isValid()){         

                $plainPassword = $form->get('password')->getData();   

                // Verificamos si el email o nick ya existen

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy([
                    'email' => $form->get('email')->getData()
                ]);

                if ($existingUser) {
                    
                    $this->addFlash('danger', 'Ya existe un usuario con este correo electrónico.');
                    return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }

                $existingNick = $this->entityManager->getRepository(User::class)->findOneBy([
                    'nick' => $form->get('nick')->getData()
                ]);

                if ($existingNick) {
                    $this->addFlash('danger', 'Ya existe un usuario con este nick.');
                    return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }

                $user->setPassword(
                    $this->userPasswordHasher->hashPassword($user, $plainPassword)
                );
                
                $user->setRole('ROLE_USER');
                $user->setImage(null);  
                $user->setActive(1);  
                $this->entityManager->persist($user);

                try {
                    
                    $this->entityManager->flush(); 

                    $this->addFlash('success', '¡Usuario registrado correctamente!');

                    return $this->redirectToRoute('app_login');  

                    
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'El email o el nick ya están en uso');

                } catch (\Throwable $e) {
                    $this->addFlash('error', 'Error al registrar usuario');
                } 

            }else{

                $this->addFlash('danger', 'El registro no se ha creado correctamente, formulario no válido');
            }        
        }

        return $this->render('registration/register.html.twig', [  
            'registrationForm' => $form->createView()
        ]);
    }

    // ===== OBTENER USUARIOS =====
    #[Route('/users', name: 'app_users')]
    public function getUsers(
        Request $request
    ): Response
    {
        //variable para saber si estamos buscando usuario
        $esBusqueda = false;

        $users = $this->entityManager->getRepository(User::class)
                                     ->createQueryBuilder('u')
                                     ->orderBy('u.id', 'DESC')
                                     ->getQuery();

        $pagination = $this->paginator->paginate(
            $users,
            $request->query->getInt('page', 1),  
            5                                   
        );

        return $this->render('user/users.html.twig', [
            'pagination' => $pagination,
            'esBusqueda'  => $esBusqueda
        ]);
    }

    // ===== COMPROBAR SI NICK EXISTE =====
    #[Route('/nick-test', name: 'nick_test')]
    public function nickTestAction(
        Request $request
    ): Response {

        $nick = $request->request->get('nick');

        $user_exist = $this->entityManager->getRepository(User::class)
                                    ->findOneBy(['nick' => $nick]);

        $result = $user_exist ? "used" : "unused";

        return new Response($result);
    }

    // ===== COMPROBAR SI EMAIL EXISTE =====
    #[Route('/email-test', name: 'email_test')]
    public function emailTestAction(
        Request $request
    ): Response {

        $email = $request->request->get('email');

        $user_exist = $this->entityManager->getRepository(User::class)
                                    ->findOneBy(['email' => $email]);

        $result = $user_exist ? "used" : "unused";

        return new Response($result);
    }

    // ===== EDITAR DATOS DE USUARIO =====
    #[Route('/my-data', name: 'user_edit')]
    public function editUser(
        Request $request
    ): Response {

        /** @var User $user */
        $user = $this->getUser(); 
        
        if (!$user) {
            $this->addFlash('error', 'Debes iniciar sesión para editar tu perfil');
            return $this->redirectToRoute('app_login');
        }

        $user_image = $user->getImage();
                                  
        $form = $this->createForm(UserType::class, $user);  
        $form->handleRequest($request);                          

        if($form->isSubmitted()){

                if($form->isValid()){    

                    // Verificamos si el email existe (excepto el usuario actual)
                    $existingEmail = $this->entityManager->getRepository(User::class)->findOneBy([
                        'email' => $form->get('email')->getData()
                    ]);

                    // Si encuentra un usuario con ese email, pero NO es el usuario actual
                    if ($existingEmail && $existingEmail->getId() !== $user->getId()) {
                        $this->addFlash('danger', 'Ya existe un usuario con este correo electrónico.');

                        return $this->render('user/edit_user.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }

                    // Verificamos si el nick existe (excepto el usuario actual)
                    $existingNick = $this->entityManager->getRepository(User::class)->findOneBy([
                        'nick' => $form->get('nick')->getData()
                    ]);

                    // Si encuentra un usuario con ese nick, pero NO es el usuario actual → error
                    if ($existingNick && $existingNick->getId() !== $user->getId()) {
                        $this->addFlash('danger', 'Ya existe un usuario con este nick.');

                        return $this->render('user/edit_user.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }

                    //Upload FILE
                    $file = $form['image']->getData();

                    if($file) {

                        $ext = $file->guessExtension();

                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($ext, $allowedExtensions)) {
                            
                            $file_name = $user->getId().time().'.'.$ext;

                            $file->move("uploads/users", $file_name);

                            $user->setImage($file_name);
                        } else {
                            
                            $this->addFlash('danger', 'Formato de imagen no válido.');
                            $user->setImage($user_image);
                        }
                    }
                
                    $this->entityManager->persist($user);
                    
                    try {

                        
                        $this->entityManager->flush(); 

                        $this->addFlash('success', '¡Has modificado tus datos correctamente!'); 
                        return $this->redirectToRoute('user_edit');
                    } catch (\Exception $e) {
                        
                        $this->addFlash('danger', 'Error al editar tus datos.');
                    }   

                }else{

                    $this->addFlash('danger', 'Los datos no se han editado correctamente, formulario no válido');
                }        
        }

        return $this->render('user/edit_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // ===== BUSCAR USUARIO =====
    #[Route('/search', name: 'user_search')]
    public function search(
        Request $request
        ): Response
    {
        
        $search = $request->query->get("search", null);

        //variable que usamos en la plantilla user/users.html.twig
        $esBusqueda = true;

        $qb = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');

        if ($search) {
            
            $searchTerm = '%' . trim($search) . '%';   

            $qb->where(
                $qb->expr()->orX(

                    // Busca a partir de estos dos campos
                    $qb->expr()->like('u.name', ':search'),
                    $qb->expr()->like('u.surname', ':search')     
                )
            )->setParameter('search', $searchTerm); // Aqui damos valor real a search
        }

        $users = $qb->orderBy('u.id', 'DESC')->getQuery();;

        $pagination = $this->paginator->paginate(
            $users,
            $request->query->getInt('page', 1),  
            5                                  
        );

        return $this->render('user/users.html.twig', [
            'pagination' => $pagination,
            'esBusqueda'  => $esBusqueda
        ]);
    }

    // ===== PERFIL DE USUARIO =====
    #[Route('/user/{nickname}', name: 'user_profile')]
    public function profile(
        Request $request,
        string $nickname
        ): Response
    {
        if($nickname != null){
            $users_repo = $this->entityManager->getRepository(User::class);
            $user = $users_repo->findOneBy(["nick" => $nickname]);
        }else{
            
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
        }
        
        if(empty($user) || !is_object($user)){
         return $this->redirectToRoute('app_home');       
        }


        $dql = "SELECT p FROM App\Entity\Publication p WHERE p.user = :user ORDER BY p.id DESC";

        $query = $this->entityManager->createQuery($dql)
                                     ->setParameter('user', $user);

        $publications = $this->paginator->paginate(
             $query,
             $request->query->getInt('page', 1),  
             5                                   
        );

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'pagination'  => $publications
        ]);
    }
}