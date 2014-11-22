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

class ExampleUserRegistrationBundle extends Bundle
{
    public function boot()
    {
        $this->configureSwiftMailer();
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
