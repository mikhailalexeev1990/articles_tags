<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use App\Entity\ArticleTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => new Assert\Length(min: 1, max: 255),
                'empty_data' => ''
            ])
            ->add('articleTags', CollectionType::class, [
                'entry_type' => ArticleTagType::class,
                'property_path' => 'articleTags',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'data_class' => ArticleTag::class,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
