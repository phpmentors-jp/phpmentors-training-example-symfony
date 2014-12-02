<?php
/*
 * Copyright (c) 2012-2013 KUBO Atsuhiro <kubo@iteman.jp>,
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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Example\UserRegistrationBundle\Entity\Factory\UserFactory;
use Example\UserRegistrationBundle\Form\Type\UserRegistrationType;
use Example\UserRegistrationBundle\Transfer\UserTransfer;
use Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase;

class UserRegistrationController extends Controller
{
    /**
     * @var string
     */
    private static $VIEW_INPUT = 'ExampleUserRegistrationBundle:UserRegistration:registration_input.html.twig';

    /**
     * @var string
     */
    private static $VIEW_CONFIRMATION = 'ExampleUserRegistrationBundle:UserRegistration:registration_confirmation.html.twig';

    /**
     * @var string
     */
    private static $VIEW_SUCCESS = 'ExampleUserRegistrationBundle:UserRegistration:registration_success.html.twig';

    /**
     * @return Response
     *
     * @Route("/users/registration/")
     * @Method("GET")
     */
    public function inputAction()
    {
        if (!$this->get('session')->has('user')) {
            $userFactory = new UserFactory();
            $user = $userFactory->create();
            $this->get('session')->set('user', $user);
        } else {
            $user = $this->get('session')->get('user');
        }

        return $this->render(self::$VIEW_INPUT, array(
            'form' => $this->createForm(new UserRegistrationType(), $user)->createView(),
        ));
    }

    /**
     * @param  Request  $request
     * @return Response
     *
     * @Route("/users/registration/")
     * @Method("POST")
     */
    public function inputPostAction(Request $request)
    {
        $form = $this->createForm(new UserRegistrationType(), $this->get('session')->get('user'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('example_userregistration_userregistration_confirmation', array(), true));
        } else {
            return $this->render(self::$VIEW_INPUT, array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @return Response
     *
     * @Route("/users/registration/confirmation")
     * @Method("GET")
     */
    public function confirmationAction()
    {
        return $this->render(self::$VIEW_CONFIRMATION, array(
            'form' => $this->createFormBuilder()->getForm()->createView(),
            'user' => $this->get('session')->get('user'),
        ));
    }

    /**
     * @param  Request  $request
     * @return Response
     *
     * @Route("/users/registration/confirmation")
     * @Method("POST")
     */
    public function confirmationPostAction(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($request->request->has('prev')) {
                return $this->redirect($this->generateUrl('example_userregistration_userregistration_input', array(), true));
            }
            $this->createUserRegistrationUsecase()->run($this->get('session')->get('user'));

            $this->get('session')->remove('user');

            return $this->redirect($this->generateUrl('example_userregistration_userregistration_success', array(), true));
        } else {
            return $this->render(self::$VIEW_CONFIRMATION, array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @return Response
     *
     * @Route("/users/registration/success")
     * @Method("GET")
     */
    public function successAction()
    {
        return $this->render(self::$VIEW_SUCCESS);
    }

    /**
     * @return \Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase
     */
    protected function createUserRegistrationUsecase()
    {
        return new UserRegistrationUsecase(
            $this->get('doctrine')->getEntityManager(),
            $this->get('security.encoder_factory')->getEncoder('Example\UserRegistrationBundle\Entity\User'),
            $this->get('security.secure_random'),
            new UserTransfer($this->get('mailer'), new \Swift_Message(), $this->get('twig'))
        );
    }
}
