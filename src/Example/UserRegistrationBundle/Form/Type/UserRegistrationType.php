<?php
/*
 * Copyright (c) 2012, 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', 'text', array('label' => '名前(姓)'))
            ->add('firstName', 'text', array('label' => '名前(名)'))
            ->add('email', 'email', array('label' => 'メールアドレス'))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_name' => 'password',
                'second_name' => 'confirmation_password',
                'invalid_message' => 'パスワードが一致しません。',
                'first_options' => array('label' => 'パスワード'),
                'second_options' => array('label' => '確認用パスワード'),
            ))
            ->add('next', 'submit', array('label' => '次へ'));
    }

    public function getName()
    {
        return 'user_registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'validation_groups' => array('registration'),
        ));
    }
}
