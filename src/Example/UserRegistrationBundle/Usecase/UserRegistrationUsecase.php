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

namespace Example\UserRegistrationBundle\Usecase;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Usecase\CommandUsecaseInterface;

use Example\UserRegistrationBundle\Entity\User;

class UserRegistrationUsecase implements CommandUsecaseInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var \Symfony\Component\Security\Core\Util\SecureRandomInterface
     */
    protected $secureRandom;

    /**
     * @param \Doctrine\ORM\EntityManager                                       $entityManager
     * @param \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $passwordEncoder
     * @param \Symfony\Component\Security\Core\Util\SecureRandomInterface       $secureRandom
     */
    public function __construct(EntityManager $entityManager, PasswordEncoderInterface $passwordEncoder, SecureRandomInterface $secureRandom)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->secureRandom = $secureRandom;
    }

    /**
     * @param  \PHPMentors\DomainKata\Entity\EntityInterface $user
     * @return mixed
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
