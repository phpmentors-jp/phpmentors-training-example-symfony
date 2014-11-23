<?php
/*
 * Copyright (c) 2012-2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Validator\ConstraintValidatorInterface;

use Example\UserRegistrationBundle\Event\BundleEvent;

class UserRegistrationControllerTest extends WebTestCase
{
    /**
     * @return EntityManagerInterface
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
        $self = $this; /* @var $self UserRegistrationControllerTest */

        入力ページの表示: {
            $postBootListener = function (BundleEvent $event) use ($self) {
                $event->getContainer()->set('doctrine.orm.default_entity_manager', $self->createEntityManager());
            };
            BundleEvent::addPostBootListener($postBootListener);
            $client = static::createClient(); /* @var $client Client */
            BundleEvent::removePostBootListener($postBootListener);

            $client->request('GET', '/users/registration/');
            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
            $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('ユーザー情報のご入力'));
        }

        ユーザー登録フォームの送信: {
            $form = $client->getCrawler()->selectButton('user_registration[next]')->form();
            $form['user_registration[lastName]'] = '山田';
            $form['user_registration[firstName]'] = '太郎';
            $form['user_registration[email]'] = 'example@example.com';
            $form['user_registration[password][password]'] = 'password';
            $form['user_registration[password][confirmation_password]'] = 'password';

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
            $client->submit($client->getCrawler()->selectButton('form[next]')->form());
            BundleEvent::removePostBootListener($postBootListener);

            $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
            $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('登録完了'));

            \Phake::verify($userRegistrationUsecase)->run($this->isInstanceOf('Example\UserRegistrationBundle\Entity\User'));
        }
    }
}
