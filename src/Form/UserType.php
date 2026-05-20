<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-name form-control'
                          ],
                
            ])
            ->add('surname', TextType::class, [
                'label' => 'Apellidos',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-surname form-control'
                          ],
                
            ])
            ->add('nick', TextType::class, [
                'label' => 'Nick',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-nick form-control'
                          ],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-email form-control',
                            'placeholder' => 'tu@email.com'
                          ],
                
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biografia',
                'required' => false,
                'attr' => [
                            'class' => 'form-bio form-control'
                          ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Foto',
                'mapped' => false,  
                'data_class' => null,
                'required' => false,
                'attr' => [
                            'class' => 'form-image form-control'
                          ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
