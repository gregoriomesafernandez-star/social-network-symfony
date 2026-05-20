<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
                            'class' => 'form-nick form-control nick-input'
                          ],
                
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-email form-control email-input',
                            'placeholder' => 'tu@email.com'
                          ],
                
            ])

            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-password form-control'
                          ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Acepto los términos y condiciones',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Deberías aceptar los términos para continuar.',
                    ),
                ],
                'attr' => [
                            'class' => 'form-check-input form-check-label ms-2'
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
