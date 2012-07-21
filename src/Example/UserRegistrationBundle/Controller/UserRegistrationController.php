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

namespace Example\UserRegistrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since      Class available since Release 1.0.0
 */
class UserRegistrationController extends Controller
{
    /**
     * @Route("/users/registration/")
     * @Method("GET")
     */
    public function inputAction()
    {
        return $this->render('ExampleUserRegistrationBundle:UserRegistration:registration_input.html.twig', array(
            'form' => $this->createFormBuilder()->getForm()->createView(),
        ));
    }

    /**
     * @Route("/users/registration/")
     * @Method("POST")
     */
    public function inputPostAction()
    {
        $form = $this->createFormBuilder()->getForm();
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            return $this->redirect($this->generateUrl('example_userregistration_userregistration_confirmation', array(), true));
        } else {
            return $this->render('ExampleUserRegistrationBundle:UserRegistration:registration_input.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/users/registration/confirmation")
     * @Method("GET")
     */
    public function confirmationAction()
    {
        return $this->render('ExampleUserRegistrationBundle:UserRegistration:registration_confirmation.html.twig', array(
            'form' => $this->createFormBuilder()->getForm()->createView(),
        ));
    }

    /**
     * @Route("/users/registration/confirmation")
     * @Method("POST")
     */
    public function confirmationPostAction()
    {
        $form = $this->createFormBuilder()->getForm();
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            if ($this->getRequest()->request->has('prev')) {
                return $this->redirect($this->generateUrl('example_userregistration_userregistration_input', array(), true));
            }

            return $this->redirect($this->generateUrl('example_userregistration_userregistration_success', array(), true));
        } else {
            return $this->render('ExampleUserRegistrationBundle:UserRegistration:registration_confirmation.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/users/registration/success")
     * @Method("GET")
     */
    public function successAction()
    {
        return $this->render('ExampleUserRegistrationBundle:UserRegistration:registration_success.html.twig');
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
