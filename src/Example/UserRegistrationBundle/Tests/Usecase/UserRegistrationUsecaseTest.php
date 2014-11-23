<?php
/*
 * Copyright (c) 2012-2013 KUBO Atsuhiro <kubo@iteman.jp>,
 *               2014 YAMANE Nana <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Tests\Usecase;

use Example\UserRegistrationBundle\Entity\User;
use Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase;

class UserRegistrationUsecaseTest extends \PHPUnit_Framework_TestCase
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

        $passwordEncoder = \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        \Phake::when($passwordEncoder)->encodePassword($this->anything(), $this->anything())->thenReturn($password);

        $activationKey = 'ACTIVATION_KEY';
        $secureRandom = \Phake::mock('Symfony\Component\Security\Core\Util\SecureRandomInterface');
        \Phake::when($secureRandom)->nextBytes($this->anything())->thenReturn($activationKey);

        $userTransfer = \Phake::mock('Example\UserRegistrationBundle\Transfer\UserTransfer');
        \Phake::when($userTransfer)->sendActivationEmail($this->anything())->thenReturn(true);

        $userRegistrationUsecase = new UserRegistrationUsecase($entityManager, $passwordEncoder, $secureRandom, $userTransfer);
        $userRegistrationUsecase->run($user);

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
        $userRegistrationUsecase = new UserRegistrationUsecase(
            $entityManager,
            \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface'),
            \Phake::mock('Symfony\Component\Security\Core\Util\SecureRandomInterface'),
            \Phake::mock('Example\UserRegistrationBundle\Transfer\UserTransfer')
        );
        $userRegistrationUsecase->activate($activationKey);

        $this->assertThat($user->getActivationDate(), $this->logicalNot($this->equalTo(null)));
        $this->assertThat($user->getActivationDate(), $this->isInstanceOf('DateTime'));

        \Phake::verify($userRepository)->findOneByActivationKey($this->equalTo($activationKey));
        \Phake::verify($entityManager)->flush();
    }
}
