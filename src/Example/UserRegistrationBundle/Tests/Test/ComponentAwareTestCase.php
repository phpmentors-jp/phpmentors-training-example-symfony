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
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 1.0.0
 */

namespace Example\UserRegistrationBundle\Tests\Test;

require_once $_SERVER['KERNEL_DIR'] . '/AppKernel.php';

use Stagehand\ComponentFactory\ComponentFactory;

/**
 * @package    PHPMentors_Training_Example_Symfony
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      Class available since Release 1.0.0
 */
abstract class ComponentAwareTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Stagehand\ComponentFactory\ComponentFactory
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

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */