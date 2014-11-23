<?php
/*
 * Copyright (c) 2012, 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Example\UserRegistrationBundle\Event\BundleEvent;

class ExampleUserRegistrationBundle extends Bundle
{
    public function boot()
    {
        foreach (BundleEvent::getPostBootListeners() as $postBootListener) {
            $this->container->get('event_dispatcher')->addListener(BundleEvent::POST_BOOT, $postBootListener);
        }
        $this->container->get('event_dispatcher')->dispatch(BundleEvent::POST_BOOT, new BundleEvent($this->container));

        $this->configureSwiftMailer();

        $this->container->set('example_user_registration.entity_manager', $this->container->get('doctrine')->getManager());
    }

    public function shutdown()
    {
        foreach (BundleEvent::getPostBootListeners() as $postBootListener) {
            $this->container->get('event_dispatcher')->removeListener(BundleEvent::POST_BOOT, $postBootListener);
        }
    }

    private function configureSwiftMailer()
    {
        \Swift::init(function () {
            \Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            \Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
        });
    }
}
