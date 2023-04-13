<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => `titre de l'article`,
            ])
                
            ->add('subtitle', TextType::class, [
                'label' => "sous-titre de l'article"
            ])
            ->add('content', TextType::class, [
                'label' => 'contenu'
            ])
            ->add('photo', FileType::class, [

            ])
            ->add('category');
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
