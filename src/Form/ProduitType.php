<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('couleur')
            ->add('taille')
            ->add('photo', FileType::class, [
                "mapped" => false,
                "required" => false,
                "attr" => [
                    'class' => 'dropify',
                    'id'    => 'input-file-now-costom-1',
                    'for'   => 'input-file-now-costom-1'
                ]
            ])
            ->add('prix')
            ->add('stock')
            ->add('categorie', EntityType::class, [
                'class'=>Categorie::class,
                'choice_label'=> 'nom',
                'placeholder' => 'choisissez une catégorie'
            ])
            ->add('envoyer', SubmitType::class)  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
