<?php

namespace App\Form;

use App\Entity\PrivateMessage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateMessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Recibimos usuario logeado desde PrivateMessageController
        $user = $options['user'];

        $builder
            ->add('receiver', EntityType::class, [
                'class' => User::class,

                'query_builder'  => function ($er) use($user){
                    return $er->getFollowingUsers($user);
                },

                'choice_label' => function ($u){
                    return $u->getName()." ".$u->getSurname()." - ".$u->getNick();
                },
                
                'label' => 'Para: ',

                'attr' => [
                            'class' => 'form-control'
                          ],

            ])
            ->add('message', TextareaType::class, [
                'label' => 'Mensaje',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-control'
                          ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Imagen',         
                'data_class' => null,
                'required' => false,
                'attr' => [
                            'class' => 'form-control'
                          ]
            ])
            ->add('file', FileType::class, [
                'label' => 'Archivo',         
                'data_class' => null,
                'required' => false,
                'attr' => [
                            'class' => 'form-control'
                          ]
            ])
            ->add('Enviar', SubmitType::class, [
                'label' => 'Enviar',
                'attr' => [
                            'class' => 'mb-3 btn btn-success'
                          ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateMessage::class,

            // Opción personalizada para pasar el usuario logueado
            'user' => null
        ]);
    }
}
