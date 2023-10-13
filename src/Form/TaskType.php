<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('content', TextareaType::class)
            ->add('user', EntityType::class, [  // champ autheur de la tache en disabled car on ne veut pas la modifier
                'class' => User::class,
                'choice_label' => 'username',
                'disabled' => true,
            ]);

    }
}
