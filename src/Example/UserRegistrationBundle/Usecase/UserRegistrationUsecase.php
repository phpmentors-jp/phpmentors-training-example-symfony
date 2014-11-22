<?php
/*
 * Copyright (c) 2012-2014 KUBO Atsuhiro <kubo@iteman.jp>,
 *               2014 YAMANE Nana <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Usecase;

use Doctrine\ORM\EntityManagerInterface;
use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Usecase\CommandUsecaseInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

use Example\UserRegistrationBundle\Entity\User;

class UserRegistrationUsecase implements CommandUsecaseInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var SecureRandomInterface
     */
    private $secureRandom;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param PasswordEncoderInterface $passwordEncoder
     * @param SecureRandomInterface    $secureRandom
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderInterface $passwordEncoder, SecureRandomInterface $secureRandom)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->secureRandom = $secureRandom;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function run(EntityInterface $user)
    {
        $user->setActivationKey(base64_encode($this->secureRandom->nextBytes(24)));
        $user->setPassword($this->passwordEncoder->encodePassword($user->getPassword(), User::SALT));
        $user->setRegistrationDate(new \DateTime());

        $this->entityManager->getRepository('Example\UserRegistrationBundle\Entity\User')->add($user);
        $this->entityManager->flush();
    }
}
