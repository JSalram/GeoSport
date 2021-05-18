<?php

namespace App\Form;

use App\Entity\Valoracion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValoracionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nota')
            ->add('comentario')
            ->add('fecha')
            ->add('usuario')
            ->add('spot')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Valoracion::class,
        ]);
    }
}
