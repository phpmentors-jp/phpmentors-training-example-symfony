<?php
/*
 * Copyright (c) 2012 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Entity\Factory;

use Example\UserRegistrationBundle\Entity\User;

class UserFactory
{
    /**
     * @return User
     */
    public function create()
    {
        return new User();
    }
}
