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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Example\UserRegistrationBundle\Entity\ActivationKey;

class UserActivationController extends Controller
{
    const VIEW_SUCCESS = 'ExampleUserRegistrationBundle:UserActivation:success.html.twig';

    /**
     * @param  Request               $request
     * @return Response
     * @throws NotFoundHttpException
     *
     * @Route("/users/registration/activation/")
     * @Method("GET")
     */
    public function activationGetAction(Request $request)
    {
        if (!$request->query->has('key')) {
            throw $this->createNotFoundException();
        }

        $this->createUserActivationUsecase()->run(new ActivationKey($request->query->get('key')));

        return $this->render(self::VIEW_SUCCESS);
    }

    /**
     * @return UsecaseInterface
     */
    private function createUserActivationUsecase()
    {
        return $this->get('example_user_registration.user_activation_usecase');
    }
}
