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

use Example\UserRegistrationBundle\Transfer\UserTransfer;
use Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase;

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

        $this->createUserRegistrationUsecase()->activate($request->query->get('key'));

        return $this->render(self::$VIEW_SUCCESS);
    }

    /**
     * @return \Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase
     */
    protected function createUserRegistrationUsecase()
    {
        return new UserRegistrationUsecase(
            $this->get('doctrine')->getManager(),
            $this->get('security.encoder_factory')->getEncoder('Example\UserRegistrationBundle\Entity\User'),
            $this->get('security.secure_random'),
            new UserTransfer($this->get('mailer'), new \Swift_Message(), $this->get('twig'))
        );
    }
}
