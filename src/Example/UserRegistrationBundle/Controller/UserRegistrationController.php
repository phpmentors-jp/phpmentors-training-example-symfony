<?php
/*
 * Copyright (c) 2012-2013 KUBO Atsuhiro <kubo@iteman.jp>,
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
        return $this->render(self::$VIEW_INPUT, array(
            'form' => $this->createFormBuilder()->getForm()->createView(),
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
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('example_userregistration_userregistration_confirmation', array(), true));
        } else {
            return $this->render(self::$VIEW_INPUT, array(
                'form' => $form->createView(),
            ));
        }
    }
}
