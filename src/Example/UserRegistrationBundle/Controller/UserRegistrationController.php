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

namespace Example\UserRegistrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users/registration/")
     * @Method("POST")
     */
    public function inputPostAction(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->submit($request);
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('example_userregistration_userregistration_confirmation', array(), true));
        } else {
            return $this->render(self::$VIEW_INPUT, array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users/registration/confirmation")
     * @Method("GET")
     */
    public function confirmationAction()
    {
        return $this->render(self::$VIEW_CONFIRMATION, array(
            'form' => $this->createFormBuilder()->getForm()->createView(),
        ));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users/registration/confirmation")
     * @Method("POST")
     */
    public function confirmationPostAction(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->submit($request);
        if ($form->isValid()) {
            if ($request->request->has('prev')) {
                return $this->redirect($this->generateUrl('example_userregistration_userregistration_input', array(), true));
            }

            return $this->redirect($this->generateUrl('example_userregistration_userregistration_success', array(), true));
        } else {
            return $this->render(self::$VIEW_CONFIRMATION, array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users/registration/success")
     * @Method("GET")
     */
    public function successAction()
    {
        return $this->render(self::$VIEW_SUCCESS);
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