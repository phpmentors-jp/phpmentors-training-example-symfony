<?php
/*
 * Copyright (c) 2012, 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_CourseRegistration.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class BundleEvent extends Event
{
    const POST_BOOT = 'example_user_registration.post_boot';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private static $postBootListeners = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $postBootListener
     */
    public static function addPostBootListener($postBootListener)
    {
        self::$postBootListeners[] = $postBootListener;
    }

    /**
     * @param mixed $postBootListener
     */
    public static function removePostBootListener($postBootListener)
    {
        $index = array_search($postBootListener, self::$postBootListeners, true);
        if ($index === false) {
            return;
        }
        unset(self::$postBootListeners[$index]);
    }

    /**
     * @return array
     */
    public static function getPostBootListeners()
    {
        return self::$postBootListeners;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
