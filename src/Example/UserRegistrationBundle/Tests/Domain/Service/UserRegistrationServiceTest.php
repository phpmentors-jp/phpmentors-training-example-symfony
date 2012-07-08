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

namespace Example\UserRegistrationBundle\Tests\Domain\Service;

use Example\UserRegistrationBundle\Domain\Service\UserRegistrationService;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since      Class available since Release 1.0.0
 */
class UserRegistrationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function ユーザーを登録する()
    {
        $userClass = 'Example\UserRegistrationBundle\Domain\Data\User';
        $userRepository = \Phake::mock('Example\UserRegistrationBundle\Domain\Data\Repository\UserRepository');
        $user = \Phake::mock($userClass);
        $entityManager = \Phake::mock('Doctrine\ORM\EntityManager');
        \Phake::when($entityManager)->getRepository($userClass)->thenReturn($userRepository);
        $userRegistrationService = new UserRegistrationService();
        $userRegistrationService->setEntityManager($entityManager);
        $userRegistrationService->register($user);

        \Phake::verify($user)->setActivationKey(\Phake::capture($activationKey));
        $this->assertThat(strlen($activationKey), $this->greaterThan(0));

        \Phake::verify($user)->setRegistrationDate($this->isInstanceOf('DateTime'));
        \Phake::verify($userRepository)->register($this->anything());
        \Phake::verify($entityManager)->flush();
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
