<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    //Builds the form
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Defines data
        $user = $this->tokenStorage->getToken()->getUser();
        $creation = $user->getCreation()->format('d/m/Y');

        $builder
            ->remove('username')
            ->remove('current_password')
            ->add('email', EmailType::class, array(
                'label' => 'label.email',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'placeholder.email',
                )))
            ->add('avatar', TextType::class, array(
                'label' => 'label.avatar',
                'disabled' => true,
                'required' => false,
                ))
            ->add('creation', TextType::class, array(
                'data' => $creation,
                'label' => 'label.creation',
                'disabled' => true,
                'required' => false,
                ))
            ->add('gender', ChoiceType::class, array(
                'label' => 'label.gender',
                'required' => false,
                'choices'  => array(
                    'label.gender' => null,
                    'label.woman' => 'woman',
                    'label.man' => 'man',
                )))
            ->add('firstname', TextType::class, array(
                'label' => 'label.firstname',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'placeholder.firstname',
                )))
            ->add('lastname', TextType::class, array(
                'label' => 'label.lastname',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'placeholder.lastname',
                )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\UserFilesBundle\Entity\User',
            'intention' => 'ProfileForm',
            'allow_extra_fields' => true,
            'translation_domain' => 'userFiles',
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'fos_user_profile_edit';
    }
}
