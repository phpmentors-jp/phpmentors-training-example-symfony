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

namespace Example\UserRegistrationBundle\Controller;

use PHPMentors\DomainKata\Usecase\UsecaseInterface;
use PHPMentors\PageflowerBundle\Annotation\Accept;
use PHPMentors\PageflowerBundle\Annotation\EndPage;
use PHPMentors\PageflowerBundle\Annotation\Init;
use PHPMentors\PageflowerBundle\Annotation\Page;
use PHPMentors\PageflowerBundle\Annotation\Pageflow;
use PHPMentors\PageflowerBundle\Annotation\StartPage;
use PHPMentors\PageflowerBundle\Annotation\Stateful;
use PHPMentors\PageflowerBundle\Annotation\Transition;
use PHPMentors\PageflowerBundle\Controller\ConversationalControllerInterface;
use PHPMentors\PageflowerBundle\Conversation\ConversationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Example\UserRegistrationBundle\Entity\User;
use Example\UserRegistrationBundle\Form\Type\UserRegistrationType;

/**
 * @Route("/users/registration", service="example_user_registration.user_registration_controller")
 * @Pageflow({
 *     @StartPage({"input",
 *         @Transition("confirmation"),
 *     }),
 *     @Page({"confirmation",
 *         @Transition("success"),
 *         @Transition("input")
 *     }),
 *     @EndPage("success")
 * })
 */
class UserRegistrationController extends Controller implements ConversationalControllerInterface
{
    const VIEW_INPUT = 'ExampleUserRegistrationBundle:UserRegistration:input.html.twig';
    const VIEW_CONFIRMATION = 'ExampleUserRegistrationBundle:UserRegistration:confirmation.html.twig';
    const VIEW_SUCCESS = 'ExampleUserRegistrationBundle:UserRegistration:success.html.twig';

    /**
     * {@inheritDoc}
     */
    private $conversationContext;

    /**
     * @var User
     *
     * @Stateful
     */
    private $user;

    /**
     * {@inheritDoc}
     */
    public function setConversationContext(ConversationContext $conversationContext)
    {
        $this->conversationContext = $conversationContext;
    }

    /**
     * @Init
     */
    public function initialize()
    {
        $this->user = new User();
    }

    /**
     * @return Response
     *
     * @Route("/")
     * @Method("GET")
     * @Accept("input")
     * @Accept("confirmation")
     */
    public function inputGetAction()
    {
        if ($this->conversationContext->getConversation()->getCurrentPage()->getPageId() == 'confirmation') {
            $this->conversationContext->getConversation()->transition('input');
        }

        $form = $this->createForm(new UserRegistrationType(), $this->user, array('action' => $this->generateUrl('example_userregistration_userregistration_inputpost'), 'method' => 'POST'));

        return $this->render(self::VIEW_INPUT, array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param  Request  $request
     * @return Response
     *
     * @Route("/")
     * @Method("POST")
     * @Accept("input")
     * @Accept("confirmation")
     */
    public function inputPostAction(Request $request)
    {
        if ($this->conversationContext->getConversation()->getCurrentPage()->getPageId() == 'confirmation') {
            $this->conversationContext->getConversation()->transition('input');
        }

        $form = $this->createForm(new UserRegistrationType(), $this->user, array('action' => $this->generateUrl('example_userregistration_userregistration_inputpost'), 'method' => 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->conversationContext->getConversation()->transition('confirmation');

            return $this->redirect($this->conversationContext->generateUrl('example_userregistration_userregistration_confirmationget'));
        } else {
            return $this->render(self::VIEW_INPUT, array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @return Response
     *
     * @Route("/confirmation")
     * @Method("GET")
     * @Accept("confirmation")
     */
    public function confirmationGetAction()
    {
        $form = $this->createFormBuilder(null, array('action' => $this->generateUrl('example_userregistration_userregistration_confirmationpost'), 'method' => 'POST'))
            ->add('prev', 'submit', array('label' => '修正する'))
            ->add('next', 'submit', array('label' => '登録する'))
            ->getForm();

        return $this->render(self::VIEW_CONFIRMATION, array(
            'form' => $form->createView(),
            'user' => $this->user,
        ));
    }

    /**
     * @param  Request  $request
     * @return Response
     *
     * @Route("/confirmation")
     * @Method("POST")
     * @Accept("confirmation")
     */
    public function confirmationPostAction(Request $request)
    {
        $form = $this->createFormBuilder(null, array('action' => $this->generateUrl('example_userregistration_userregistration_confirmationpost'), 'method' => 'POST'))
            ->add('prev', 'submit', array('label' => '修正する'))
            ->add('next', 'submit', array('label' => '登録する'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->get('prev')->isClicked()) {
                return $this->redirect($this->conversationContext->generateUrl('example_userregistration_userregistration_inputget'));
            }

            if ($form->get('next')->isClicked()) {
                $this->createUserRegistrationUsecase()->run($this->user);
                $this->conversationContext->getConversation()->transition('success');

                return $this->render(self::VIEW_SUCCESS);
            }
        }

        $this->conversationContext->getConversation()->transition('input');

        return $this->render(self::VIEW_CONFIRMATION, array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @return UsecaseInterface
     */
    private function createUserRegistrationUsecase()
    {
        return $this->get('example_user_registration.user_registration_usecase');
    }
}
