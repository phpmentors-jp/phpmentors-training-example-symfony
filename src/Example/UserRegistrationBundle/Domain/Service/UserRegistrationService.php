<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5.3
 *
 * Copyright (c) 2012 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since      File available since Release 1.0.0
 */

namespace Example\UserRegistrationBundle\Domain\Service;

use Doctrine\ORM\EntityManager;

use Example\UserRegistrationBundle\Domain\Data\Transfer\UserTransfer;
use Example\UserRegistrationBundle\Domain\Data\User;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since      Class available since Release 1.0.0
 */
class UserRegistrationService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Example\UserRegistrationBundle\Domain\Data\Transfer\UserTransfer
     */
    protected $userTransfer;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Example\UserRegistrationBundle\Domain\Data\Transfer\UserTransfer $userTransfer
     */
    public function setUserTransfer(UserTransfer $userTransfer)
    {
        $this->userTransfer = $userTransfer;
    }

    /**
     * @param \Example\UserRegistrationBundle\Domain\Data\User $user
     * @throws \UnexpectedValueException
     */
    public function register(User $user)
    {
        $user->setActivationKey($this->generateActivationKey());
        $user->setRegistrationDate(new \DateTime());

        $this->entityManager->getRepository('Example\UserRegistrationBundle\Domain\Data\User')->register($user);
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
        $user = $this->entityManager->getRepository('Example\UserRegistrationBundle\Domain\Data\User')->findByActivationKey($activationKey);
        if (is_null($user)) {
            throw new \UnexpectedValueException('アクティベーションキーが見つかりません。');
        }

        if (!is_null($user->getActivationDate())) {
            throw new \UnexpectedValueException('ユーザーはすでに有効です。');
        }

        $user->setActivationDate(new \DateTime());
        $this->entityManager->flush();
    }

    /**
     * @return string
     * @throws \UnexpectedValueException
     * @see \Symfony\Component\Security\Http\RememberMe::generateRandomValue()
     */
    protected function generateActivationKey()
    {
        $bytes = openssl_random_pseudo_bytes(24, $strong);
        if ($strong === true && $bytes !== false) {
            return base64_encode($bytes);
        } else {
            throw new \UnexpectedValueException('アクティベーションキーの生成に失敗しました。');
        }
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
