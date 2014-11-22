<?php
/*
 * Copyright (c) 2012-2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Transfer;

use Example\UserRegistrationBundle\Entity\User;

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
    private static $ACTIVATION_EMAIL_TEMPLATE = 'ExampleUserRegistrationBundle:UserActivation:email.txt.twig';

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Swift_Message
     */
    private $messageFactory;

    /**
     * @var \Twig_Environment
     */
    private $templateLoader;

    /**
     * @param \Swift_Mailer     $mailer
     * @param \Swift_Message    $messageFactory
     * @param \Twig_Environment $templateLoader
     */
    public function __construct(\Swift_Mailer $mailer, \Swift_Message $messageFactory, \Twig_Environment $templateLoader)
    {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
        $this->templateLoader = $templateLoader;
    }

    /**
     * @param  User    $user
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
                    'activationURI' => self::$ACTIVATION_EMAIL_ACTIVATION_URI.'?key='.rawurlencode($user->getActivationKey()),
                )))
        );

        return $sentRecipientCount == 1;
    }
}
