<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5.3
 *
 * Copyright (c) 2012-2013 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 1.0.0
 */

namespace Example\UserRegistrationBundle\Tests\Usecase;

use Example\UserRegistrationBundle\Entity\User;
use Example\UserRegistrationBundle\Tests\Test\ComponentAwareTestCase;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
class UserRegistrationUsecaseTest extends ComponentAwareTestCase
{
    /**
     * @test
     */
    public function ユーザーを登録する()
    {
        $userClass = 'Example\UserRegistrationBundle\Entity\User';
        $userRepository = \Phake::mock('Example\UserRegistrationBundle\Repository\UserRepository');
        $password = 'PASSWORD';
        $user = \Phake::mock($userClass);
        \Phake::when($user)->getPassword()->thenReturn($password);

        $entityManager = \Phake::mock('Doctrine\ORM\EntityManager');
        \Phake::when($entityManager)->getRepository($userClass)->thenReturn($userRepository);
        $this->setComponent('example_user_registration.entity_manager', $entityManager);

        $passwordEncoder = \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        \Phake::when($passwordEncoder)->encodePassword($this->anything(), $this->anything())->thenReturn($password);
        $this->setComponent('example_user_registration.password_encoder', $passwordEncoder);

        $activationKey = 'ACTIVATION_KEY';
        $secureRandom = \Phake::mock('Symfony\Component\Security\Core\Util\SecureRandomInterface');
        \Phake::when($secureRandom)->nextBytes($this->anything())->thenReturn($activationKey);
        $this->setComponent('security.secure_random', $secureRandom);

        $userTransfer = \Phake::mock('Example\UserRegistrationBundle\Transfer\UserTransfer');
        \Phake::when($userTransfer)->sendActivationEmail($this->anything())->thenReturn(true);
        $this->setComponent('example_user_registration.user_transfer', $userTransfer);

        $this->createComponent('example_user_registration.user_registration_usecase')->run($user);

        \Phake::verify($secureRandom)->nextBytes($this->isType(\PHPUnit_Framework_Constraint_IsType::TYPE_INT));
        \Phake::verify($user)->setActivationKey($this->equalTo(base64_encode($activationKey)));
        \Phake::verify($user)->getPassword();
        \Phake::verify($passwordEncoder)->encodePassword($this->isType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING), $this->isType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING));
        \Phake::verify($user)->setPassword($this->isType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING));
        \Phake::verify($user)->setRegistrationDate($this->isInstanceOf('DateTime'));
        \Phake::verify($userRepository)->add($this->identicalTo($user));
        \Phake::verify($entityManager)->flush();
        \Phake::verify($userTransfer)->sendActivationEmail($this->identicalTo($user));
    }

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
        $entityManager = \Phake::mock('Doctrine\ORM\EntityManager');
        \Phake::when($entityManager)->getRepository($userClass)->thenReturn($userRepository);
        $this->setComponent('example_user_registration.entity_manager', $entityManager);

        $this->setComponent('example_user_registration.password_encoder', \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface'));
        $this->setComponent('security.secure_random', \Phake::mock('Symfony\Component\Security\Core\Util\SecureRandomInterface'));
        $this->setComponent('example_user_registration.user_transfer', \Phake::mock('Example\UserRegistrationBundle\Transfer\UserTransfer'));

        $this->createComponent('example_user_registration.user_registration_usecase')->activate($activationKey);

        $this->assertThat($user->getActivationDate(), $this->logicalNot($this->equalTo(null)));
        $this->assertThat($user->getActivationDate(), $this->isInstanceOf('DateTime'));

        \Phake::verify($userRepository)->findOneByActivationKey($this->equalTo($activationKey));
        \Phake::verify($entityManager)->flush();
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
