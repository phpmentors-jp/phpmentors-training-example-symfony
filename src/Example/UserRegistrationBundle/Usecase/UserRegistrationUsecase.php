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

namespace Example\UserRegistrationBundle\Usecase;

use Doctrine\ORM\EntityManager;
use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Usecase\CommandUsecaseInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

use Example\UserRegistrationBundle\Transfer\UserTransfer;
use Example\UserRegistrationBundle\Entity\User;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
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
     * @var \Example\UserRegistrationBundle\Transfer\UserTransfer
     */
    protected $userTransfer;

    /**
     * @param \Doctrine\ORM\EntityManager                                       $entityManager
     * @param \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $passwordEncoder
     * @param \Symfony\Component\Security\Core\Util\SecureRandomInterface       $secureRandom
     * @param \Example\UserRegistrationBundle\Transfer\UserTransfer $userTransfer
     */
    public function __construct(EntityManager $entityManager, PasswordEncoderInterface $passwordEncoder, SecureRandomInterface $secureRandom, UserTransfer $userTransfer)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->secureRandom = $secureRandom;
        $this->userTransfer = $userTransfer;
    }

    /**
     * @param  \PHPMentors\DomainKata\Entity\EntityInterface $user
     * @throws \UnexpectedValueException
     */
    public function run(EntityInterface $user)
    {
        $user->setActivationKey(base64_encode($this->secureRandom->nextBytes(24)));
        $user->setPassword($this->passwordEncoder->encodePassword($user->getPassword(), User::SALT));
        $user->setRegistrationDate(new \DateTime());

        $this->entityManager->getRepository('Example\UserRegistrationBundle\Entity\User')->add($user);
        $this->entityManager->flush();

        $emailSent = $this->userTransfer->sendActivationEmail($user);
        if (!$emailSent) {
            throw new \UnexpectedValueException('アクティベーションメールの送信に失敗しました。');
        }
    }

    /**
     * @param string $activationKey
     */
    public function activate($activationKey)
    {
        $user = $this->entityManager->getRepository('Example\UserRegistrationBundle\Entity\User')->findOneByActivationKey($activationKey);
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

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
