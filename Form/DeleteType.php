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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DeleteType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Defines data
        $user = $this->tokenStorage->getToken()->getUser();

        $builder
            ->remove('username')
            ->add('email', null, array(
                'label' => 'label.email',
                'required' => true,
                'disabled' => true,
                ))
            ->add('firstname', TextType::class, array(
                'label' => 'label.firstname',
                'required' => true,
                'disabled' => true,
                ))
            ->add('lastname', TextType::class, array(
                'label' => 'label.lastname',
                'required' => false,
                'disabled' => true,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\UserFilesBundle\Entity\User',
            'intention' => 'DeleteForm',
            'translation_domain' => 'userFiles',
        ));
    }

    public function getBlockPrefix()
    {
        return 'delete';
    }
}