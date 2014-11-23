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

namespace Example\UserRegistrationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;

use Example\UserRegistrationBundle\Event\BundleEvent;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012-2013 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
class UserRegistrationControllerTest extends WebTestCase
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function createEntityManager()
    {
        $entityManager = \Phake::mock('Doctrine\ORM\EntityManager');
        $metadataFactory = \Phake::mock('Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory');
        \Phake::when($metadataFactory)->getLoadedMetadata()->thenReturn(array());
        \Phake::when($entityManager)->getMetadataFactory()->thenReturn($metadataFactory);

        return $entityManager;
    }

    /**
     * @test
     */
    public function ユーザーを登録する()
    {
        $self = $this; /* @var $self \Example\UserRegistrationBundle\Tests\Controller\UserRegistrationControllerTest */

        入力ページの表示: {
            $postBootListener = function (BundleEvent $event) use ($self) {
                $event->getContainer()->set('doctrine.orm.default_entity_manager', $self->createEntityManager());
            };
            BundleEvent::addPostBootListener($postBootListener);
            $client = static::createClient(); /* @var $client \Symfony\Component\BrowserKit\Client */
            BundleEvent::removePostBootListener($postBootListener);

            $client->request('GET', '/users/registration/');
            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
            $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('ユーザー情報のご入力'));
        }

        ユーザー登録フォームの送信: {
            $form = $client->getCrawler()->selectButton('next')->form();
            $form['userregistration[lastName]'] = '山田';
            $form['userregistration[firstName]'] = '太郎';
            $form['userregistration[email]'] = 'example@example.com';
            $form['userregistration[password][password]'] = 'password';
            $form['userregistration[password][confirmation_password]'] = 'password';

            $postBootListener = function (BundleEvent $event) use ($self) {
                $entityManager = $self->createEntityManager();
                $classMetadataInfo = \Phake::mock('\Doctrine\ORM\Mapping\ClassMetadataInfo');
                \Phake::when($classMetadataInfo)->hasField($self->anything())->thenReturn(true);
                \Phake::when($entityManager)->getClassMetadata($self->anything())->thenReturn($classMetadataInfo);
                $event->getContainer()->set('doctrine.orm.default_entity_manager', $entityManager);

                $event->getContainer()->set('doctrine.orm.validator.unique', \Phake::mock('Symfony\Component\Validator\ConstraintValidatorInterface'));
            };
            BundleEvent::addPostBootListener($postBootListener);
            $client->submit($form);
            BundleEvent::removePostBootListener($postBootListener);

            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(302));
            $client->request('GET', $client->getResponse()->headers->get('Location'));
            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
            $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('登録内容のご確認'));
        }

        確認フォームの送信: {
            $userRegistrationUsecase = \Phake::mock('Example\UserRegistrationBundle\Usecase\UserRegistrationUsecase');
            $postBootListener = function (BundleEvent $event) use ($self, $userRegistrationUsecase) {
                $event->getContainer()->set('example_user_registration.user_registration_usecase', $userRegistrationUsecase);
            };
            BundleEvent::addPostBootListener($postBootListener);
            $client->submit($client->getCrawler()->selectButton('next')->form());
            BundleEvent::removePostBootListener($postBootListener);

            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(302));
            $client->request('GET', $client->getResponse()->headers->get('Location'));
            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
            $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('登録完了'));

            \Phake::verify($userRegistrationUsecase)->run($this->isInstanceOf('Example\UserRegistrationBundle\Entity\User'));
        }
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