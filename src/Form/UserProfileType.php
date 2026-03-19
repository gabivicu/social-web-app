<?php

namespace App\Form;

use App\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'Profile Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG or WebP).',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
            ])
            ->add('twitterUsername', TextType::class, [
                'label' => 'Twitter Username',
                'required' => false,
            ])
            ->add('websiteUrl', UrlType::class, [
                'label' => 'Website URL',
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'required' => false,
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'Date of Birth',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Settings',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }
}
