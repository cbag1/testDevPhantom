<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    TextareaType
};

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le titre ne peut pas être vide.']),
                new Assert\Length([
                    'min' => 5,
                    'max' => 255,
                    'minMessage' => 'Le titre doit contenir au moins 5 caractères.',
                    'maxMessage' => 'Le titre ne peut pas contenir plus de  255 caractères.',
                ]),
            ],
        ])
        ->add('content', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le contenu ne peut pas être vide.']),
                new Assert\Length([
                    'min' => 10,
                    'minMessage' => 'Le contenu doit contenir au moins 10 caractères.',
                ]),
            ],
        ])
        ->add('archived');
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
