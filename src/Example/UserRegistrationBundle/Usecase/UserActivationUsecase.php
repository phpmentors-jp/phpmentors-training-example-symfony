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

namespace Example\UserRegistrationBundle\Usecase;

use Doctrine\ORM\EntityManagerInterface;
use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Entity\SingleValueInterface;
use PHPMentors\DomainKata\Usecase\CommandUsecaseInterface;

use Example\UserRegistrationBundle\Entity\User;

class UserActivationUsecase implements CommandUsecaseInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \UnexpectedValueException
     */
    public function run(EntityInterface $activationKey)
    {
        /* @var $activationKey SingleValueInterface */
        assert($activationKey instanceof SingleValueInterface);

        $user = $this->entityManager->getRepository('Example\UserRegistrationBundle\Entity\User')->findOneByActivationKey($activationKey->getValue());
        if (is_null($user)) {
            throw new \UnexpectedValueException('アクティベーションキーが見つかりません。');
        }

        if (!is_null($user->getActivationDate())) {
            throw new \UnexpectedValueException('ユーザーはすでに有効です。');
        }

        $user->setActivationDate(new \DateTime());
        $this->entityManager->flush();
    }
}
