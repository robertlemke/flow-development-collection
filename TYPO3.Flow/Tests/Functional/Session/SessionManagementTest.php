<?php
namespace TYPO3\Flow\Tests\Functional\Session;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Mvc\Routing\Route;

/**
 * Test suite for the Session Management
 */
class SessionManagementTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $route = new Route();
        $route->setName('Functional Test - Session::SessionTest');
        $route->setUriPattern('test/session(/{@action})');
        $route->setDefaults(array(
            '@package' => 'TYPO3.Flow',
            '@subpackage' => 'Tests\Functional\Session\Fixtures',
            '@controller' => 'SessionTest',
            '@action' => 'sessionStart',
            '@format' => 'html'
        ));
        $this->router->addRoute($route);
    }

    /**
     * @test
     */
    public function objectManagerAlwaysReturnsTheSameSessionIfInterfaceIsSpecified()
    {
        $session1 = $this->objectManager->get(\TYPO3\Flow\Session\SessionInterface::class);
        $session2 = $this->objectManager->get(\TYPO3\Flow\Session\SessionInterface::class);
        $this->assertSame($session1, $session2);
    }

    /**
     * @test
     */
    public function objectManagerAlwaysReturnsANewSessionInstanceIfClassNameIsSpecified()
    {
        $session1 = $this->objectManager->get(\TYPO3\Flow\Session\Session::class);
        $session2 = $this->objectManager->get(\TYPO3\Flow\Session\Session::class);
        $this->assertNotSame($session1, $session2);
    }

    /**
     * Checks if getCurrentSessionSession() returns the one and only session which can also
     * be retrieved through Dependency Injection using the SessionInterface.
     *
     * @test
     */
    public function getCurrentSessionReturnsTheCurrentlyActiveSession()
    {
        $injectedSession = $this->objectManager->get(\TYPO3\Flow\Session\SessionInterface::class);
        $sessionManager = $this->objectManager->get(\TYPO3\Flow\Session\SessionManagerInterface::class);
        $otherInjectedSession = $this->objectManager->get(\TYPO3\Flow\Session\SessionInterface::class);

        $retrievedSession = $sessionManager->getCurrentSession();
        $this->assertSame($injectedSession, $retrievedSession);
        $this->assertSame($otherInjectedSession, $retrievedSession);
    }

    /**
     * Makes sure that the functional base testcase initializes an HTTP request and
     * an HTTP response which can be retrieved from the special request handler by
     * the session initialization in order to retrieve or set the session cookie.
     *
     * See bug #43590
     *
     * @test
     */
    public function aSessionCanBeStartedInAFunctionalTest()
    {
        $session = $this->objectManager->get(\TYPO3\Flow\Session\SessionInterface::class);
        $session->start();
        // dummy assertion to avoid PHPUnit warning
        $this->assertTrue(true);
    }

    /**
     * This test makes sure that if a session is used through the HTTP Browser in
     * a functional test, the Session does not have side effects which result, for
     * example, in a cookie sent only at the end of the first request.
     *
     * @test
     */
    public function aSessionUsedInAFunctionalTestVirtualBrowserSendsCookiesOnEachRequest()
    {
        $response = $this->browser->request('http://localhost/test/session');
        $this->assertTrue($response->hasCookie('TYPO3_Flow_Session'));

        $response = $this->browser->request('http://localhost/test/session');
        $this->assertTrue($response->hasCookie('TYPO3_Flow_Session'));
    }
}
