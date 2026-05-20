<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Mensaje',
                'required' => 'required',
                'attr' => [
                            'class' => 'form-control',
                            'rows' => 5,           
                            'placeholder' => '¿Qué estás pensando hoy?'
                          ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Foto',
                'mapped' => false,          
                'data_class' => null,
                'required' => false,
                'attr' => [
                            'class' => 'form-control'
                          ]
            ])
            ->add('document', FileType::class, [
                'label' => 'Documento',
                'mapped' => false,           
                'data_class' => null,
                'required' => false,
                'attr' => [
                            'class' => 'form-control'
                          ]
            ])
            ->add('Enviar', SubmitType::class, [
                'label' => 'Publicar',
                'attr' => [
                            'class' => 'btn btn-success'
                          ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
