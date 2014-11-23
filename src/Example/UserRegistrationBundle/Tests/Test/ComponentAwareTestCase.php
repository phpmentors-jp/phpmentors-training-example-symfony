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

namespace Example\UserRegistrationBundle\Tests\Test;

require_once $_SERVER['KERNEL_DIR'].'/AppKernel.php';

use Stagehand\ComponentFactory\ComponentFactory;

abstract class ComponentAwareTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ComponentFactory
     */
    private static $componentFactory;

    public static function setUpBeforeClass()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        self::$componentFactory = new ComponentFactory();
        self::$componentFactory->setContainer($kernel->getContainer());
    }

    protected function setUp()
    {
        if (!$this->checkErrorToException()) {
            $this->enableErrorToException();
        }
    }

    protected function tearDown()
    {
        self::$componentFactory->clearComponents();
    }

    public function enableErrorToException()
    {
        set_error_handler(array($this, 'errorToException'), error_reporting());
    }

    /**
     * @param  integer         $code
     * @param  string          $message
     * @param  string          $file
     * @param  integer         $line
     * @throws \ErrorException
     */
    public function errorToException($code, $message, $file, $line)
    {
        if (error_reporting() & $code) {
            throw new \ErrorException($message, 0, $code, $file, $line);
        }
    }

    /**
     * @return boolean
     */
    public function checkErrorToException()
    {
        $oldErrorHandler = set_error_handler(function () {});
        $result = is_object($oldErrorHandler) && $oldErrorHandler === array($this, 'errorToException');
        restore_error_handler();

        return $result;
    }

    /**
     * @param  string $componentID
     * @return mixed
     */
    protected function createComponent($componentID)
    {
        return self::$componentFactory->create($componentID, true);
    }

    /**
     * @param string $componentID
     * @param mixed  $component
     */
    protected function setComponent($componentID, $component)
    {
        self::$componentFactory->set($componentID, $component, true);
    }
}
