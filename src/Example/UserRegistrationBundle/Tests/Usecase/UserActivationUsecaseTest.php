<?php
/*
 * Copyright (c) 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Tests\Usecase;

use Example\UserRegistrationBundle\Entity\ActivationKey;
use Example\UserRegistrationBundle\Entity\User;
use Example\UserRegistrationBundle\Tests\Test\ComponentAwareTestCase;

class UserActivationUsecaseTest extends ComponentAwareTestCase
{
    /**
     * @test
     */
    public function ユーザーを有効にする()
    {
        $activationKey = 'ACTIVATION_KEY';
        $user = new User();
        $user->setActivationKey($activationKey);
        $userClass = get_class($user);
        $userRepository = \Phake::mock('Example\UserRegistrationBundle\Repository\UserRepository');
        \Phake::when($userRepository)->findOneByActivationKey($this->anything())->thenReturn($user);
        $entityManager = \Phake::mock('Doctrine\ORM\EntityManagerInterface');
        \Phake::when($entityManager)->getRepository($userClass)->thenReturn($userRepository);
        $this->setComponent('example_user_registration.entity_manager', $entityManager);

        $this->createComponent('example_user_registration.user_activation_usecase')->run(new ActivationKey($activationKey));

        $this->assertThat($user->getActivationDate(), $this->logicalNot($this->equalTo(null)));
        $this->assertThat($user->getActivationDate(), $this->isInstanceOf('DateTime'));

        \Phake::verify($userRepository)->findOneByActivationKey($this->equalTo($activationKey));
        \Phake::verify($entityManager)->flush();
    }
}
