<?php
namespace TYPO3\Flow\Tests\Functional\Http\Client;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Functional tests for the HTTP browser
 */
class BrowserTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @var boolean
     */
    protected $testableSecurityEnabled = true;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->registerRoute(
            'Functional Test - Http::Client::BrowserTest',
            'test/http/redirecting(/{@action})',
            array(
                '@package' => 'TYPO3.Flow',
                '@subpackage' => 'Tests\Functional\Http\Fixtures',
                '@controller' => 'Redirecting',
                '@action' => 'fromHere',
                '@format' => 'html'
            )
        );
    }

    /**
     * Check if the browser can handle redirects
     *
     * @test
     */
    public function redirectsAreFollowed()
    {
        $response = $this->browser->request('http://localhost/test/http/redirecting');
        $this->assertEquals('arrived.', $response->getContent());
    }

    /**
     * Check if the browser doesn't follow redirects if told so
     *
     * @test
     */
    public function redirectsAreNotFollowedIfSwitchedOff()
    {
        $this->browser->setFollowRedirects(false);
        $response = $this->browser->request('http://localhost/test/http/redirecting');
        $this->assertNotContains('arrived.', $response->getContent());
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals('http://localhost/test/http/redirecting/tohere', $response->getHeader('Location'));
    }
}
