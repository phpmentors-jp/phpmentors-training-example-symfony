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

namespace Example\UserRegistrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserActivationController extends Controller
{
    /**
     * @var string
     */
    private static $VIEW_SUCCESS = 'ExampleUserRegistrationBundle:UserRegistration:activation_success.html.twig';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Route("/users/registration/activation/")
     * @Method("GET")
     */
    public function activationAction(Request $request)
    {
        if (!$request->request->has('key')) {
            throw $this->createNotFoundException();
        }

        $this->get('example_user_registration.user_registration_usecase')->activate($request->query->get('key'));

        return $this->render(self::$VIEW_SUCCESS);
    }
}
