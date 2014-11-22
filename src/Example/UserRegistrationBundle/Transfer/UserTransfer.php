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

namespace Example\UserRegistrationBundle\Transfer;

use Example\UserRegistrationBundle\Entity\User;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
class UserTransfer
{
    /**
     * @var string
     */
    private static $ACTIVATION_EMAIL_ACTIVATION_URI = 'https://www.example.org/users/registration/activation/';

    /**
     * @var array
     */
    private static $ACTIVATION_EMAIL_FROM = array('noreply@phpmentors.jp' => 'Exampleサービス');

    /**
     * @var string
     */
    private static $ACTIVATION_EMAIL_SUBJECT = 'Exampleサービス: ユーザー登録のご確認および完了手続きのご案内';

    /**
     * @var string
     */
    private static $ACTIVATION_EMAIL_TEMPLATE = 'ExampleUserRegistrationBundle:UserRegistration:activation_email.txt.twig';

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Swift_Message
     */
    protected $messageFactory;

    /**
     * @var \Twig_Environment
     */
    protected $templateLoader;

    /**
     * @param \Swift_Mailer  $mailer
     * @param \Swift_Message $messageFactory
     * @param \Twig_Environment $templateLoader
     */
    public function __construct(\Swift_Mailer $mailer, \Swift_Message $messageFactory, \Twig_Environment $templateLoader)
    {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
        $this->templateLoader = $templateLoader;
    }

    /**
     * @param  \Example\UserRegistrationBundle\Entity\User $user
     * @return boolean
     */
    public function sendActivationEmail(User $user)
    {
        $sentRecipientCount = $this->mailer->send($this->messageFactory->newInstance()
                ->setCharset('iso-2022-jp')
                ->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'))
                ->setFrom(self::$ACTIVATION_EMAIL_FROM)
                ->setTo($user->getEmail())
                ->setSubject(self::$ACTIVATION_EMAIL_SUBJECT)
                ->setBody($this->templateLoader->loadTemplate(self::$ACTIVATION_EMAIL_TEMPLATE)->render(array(
                    'user' => $user,
                    'activationURI' => self::$ACTIVATION_EMAIL_ACTIVATION_URI . '?key=' . rawurlencode($user->getActivationKey()),
                )))
        );

        return $sentRecipientCount == 1;
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