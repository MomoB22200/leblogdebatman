<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            //champ photo
            ->add('photo', FileType::class, [
                'label' => 'sélectionner une nouvelle photo',
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'vous devez sélectionner un fichier',
                    ]),
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'fichier trop volumineux ! 5 MO maximum.',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'L\'image doit etre de type jpg ou png',
                    ]),
                ],
            ])


            //bouton de validation
            ->add('save', SubmitType::class, [
                'label' => 'changer la photo',
                'attr' => [
                    'class' => 'btn btn-outline-primary w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
