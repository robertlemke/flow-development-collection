<?php
namespace TYPO3\Flow\Tests\Unit\Security\Authentication;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\Authentication\AuthenticationProviderManager;
use TYPO3\Flow\Security\Authentication\TokenInterface;
use TYPO3\Flow\Security\Context;
use TYPO3\Flow\Session\SessionInterface;

/**
 * Test case for authentication provider manager
 */
class AuthenticationProviderManagerTest extends UnitTestCase
{
    /**
     * @var AuthenticationProviderManager
     */
    protected $authenticationProviderManager;

    /**
     * @var SessionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSecurityContext;

    /**
     * Sets up this test case
     */
    public function setUp()
    {
        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('dummy'), array(), '', false);
        $this->mockSession = $this->getMockBuilder(\TYPO3\Flow\Session\SessionInterface::class)->getMock();
        $this->inject($this->authenticationProviderManager, 'session', $this->mockSession);

        $this->mockSecurityContext = $this->getMockBuilder(\TYPO3\Flow\Security\Context::class)->disableOriginalConstructor()->getMock();
        $this->inject($this->authenticationProviderManager, 'securityContext', $this->mockSecurityContext);
    }

    /**
     * @test
     */
    public function authenticateDelegatesAuthenticationToTheCorrectProvidersInTheCorrectOrder()
    {
        $mockProvider1 = $this->getMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderInterface::class, array(), array(), 'mockAuthenticationProvider1');
        $mockProvider2 = $this->getMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderInterface::class, array(), array(), 'mockAuthenticationProvider2');
        $mockToken1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), 'mockAuthenticationToken1');
        $mockToken2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), 'mockAuthenticationToken2');

        $mockToken1->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(true));
        $mockToken2->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(true));
        $mockToken1->expects($this->any())->method('getAuthenticationStatus')->will($this->returnValue(TokenInterface::AUTHENTICATION_NEEDED));
        $mockToken2->expects($this->any())->method('getAuthenticationStatus')->will($this->returnValue(TokenInterface::AUTHENTICATION_NEEDED));

        $mockProvider1->expects($this->atLeastOnce())->method('canAuthenticate')->will($this->onConsecutiveCalls(true, false));
        $mockProvider2->expects($this->atLeastOnce())->method('canAuthenticate')->will($this->returnValue(true));

        $mockProvider1->expects($this->once())->method('authenticate')->with($mockToken1);
        $mockProvider2->expects($this->once())->method('authenticate')->with($mockToken2);

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationStrategy')->will($this->returnValue(Context::AUTHENTICATE_ALL_TOKENS));
        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue(array($mockToken1, $mockToken2)));

        $this->inject($this->authenticationProviderManager, 'providers', array($mockProvider1, $mockProvider2));

        $this->authenticationProviderManager->authenticate();
    }

    /**
     * @test
     */
    public function authenticateTagsSessionWithAccountIdentifier()
    {
        $account = new Account();
        $account->setAccountIdentifier('admin');

        $securityContext = $this->getMock(\TYPO3\Flow\Security\Context::class, array('getAuthenticationStrategy', 'getAuthenticationTokens', 'refreshTokens'), array(), '', false);

        $token = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class);
        $token->expects($this->any())->method('getAccount')->will($this->returnValue($account));

        $token->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(true));
        $securityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue(array($token)));

        $this->mockSession->expects($this->once())->method('addTag')->with('TYPO3-Flow-Security-Account-21232f297a57a5a743894a0e4a801fc3');

        $this->authenticationProviderManager->_set('providers', array());
        $this->authenticationProviderManager->_set('securityContext', $securityContext);

        $this->authenticationProviderManager->authenticate();
    }

    /**
     * @test
     */
    public function authenticateAuthenticatesOnlyTokensWithStatusAuthenticationNeeded()
    {
        $mockProvider = $this->getMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderInterface::class);
        $mockToken1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), 'mockAuthenticationToken11');
        $mockToken2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), 'mockAuthenticationToken12');
        $mockToken3 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), 'mockAuthenticationToken13');

        $mockToken1->expects($this->any())->method('isAuthenticated')->will($this->returnValue(false));
        $mockToken2->expects($this->any())->method('isAuthenticated')->will($this->returnValue(false));
        $mockToken3->expects($this->any())->method('isAuthenticated')->will($this->returnValue(true));

        $mockToken1->expects($this->any())->method('getAuthenticationStatus')->will($this->returnValue(TokenInterface::WRONG_CREDENTIALS));
        $mockToken2->expects($this->any())->method('getAuthenticationStatus')->will($this->returnValue(TokenInterface::NO_CREDENTIALS_GIVEN));
        $mockToken3->expects($this->any())->method('getAuthenticationStatus')->will($this->returnValue(TokenInterface::AUTHENTICATION_NEEDED));

        $mockProvider->expects($this->any())->method('canAuthenticate')->will($this->returnValue(true));
        $mockProvider->expects($this->once())->method('authenticate')->with($mockToken3);

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationStrategy')->will($this->returnValue(Context::AUTHENTICATE_ONE_TOKEN));
        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue(array($mockToken1, $mockToken2, $mockToken3)));

        $this->inject($this->authenticationProviderManager, 'providers', array($mockProvider));

        $this->authenticationProviderManager->authenticate();
    }

    /**
     * @test
     * @expectedException \TYPO3\Flow\Security\Exception\AuthenticationRequiredException
     */
    public function authenticateThrowsAnExceptionIfNoTokenCouldBeAuthenticated()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class);
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class);

        $token1->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(false));
        $token2->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(false));

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue(array($token1, $token2)));

        $this->inject($this->authenticationProviderManager, 'providers', array());

        $this->authenticationProviderManager->authenticate();
    }

    /**
     * @test
     * @expectedException \TYPO3\Flow\Security\Exception\AuthenticationRequiredException
     */
    public function authenticateThrowsAnExceptionIfAuthenticateAllTokensIsTrueButATokenCouldNotBeAuthenticated()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class);
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class);

        $token1->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(true));
        $token2->expects($this->atLeastOnce())->method('isAuthenticated')->will($this->returnValue(false));

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue(array($token1, $token2)));
        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationStrategy')->will($this->returnValue(Context::AUTHENTICATE_ALL_TOKENS));

        $this->inject($this->authenticationProviderManager, 'providers', array());

        $this->authenticationProviderManager->authenticate();
    }

    /**
     * @test
     */
    public function isAuthenticatedReturnsTrueIfAnTokenCouldBeAuthenticated()
    {
        $mockToken = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $mockToken->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));

        $this->mockSecurityContext->expects($this->once())->method('getAuthenticationTokens')->will($this->returnValue(array($mockToken)));

        $this->assertTrue($this->authenticationProviderManager->isAuthenticated());
    }

    /**
     * @test
     */
    public function isAuthenticatedReturnsFalseIfNoTokenIsAuthenticated()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token1->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token2->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));

        $authenticationTokens = array($token1, $token2);

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue($authenticationTokens));

        $this->assertFalse($this->authenticationProviderManager->isAuthenticated());
    }

    /**
     * @test
     */
    public function isAuthenticatedReturnsTrueIfAtLeastOneTokenIsAuthenticated()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token1->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token2->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));

        $authenticationTokens = array($token1, $token2);

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue($authenticationTokens));

        $this->assertTrue($this->authenticationProviderManager->isAuthenticated());
    }

    /**
     * @test
     */
    public function isAuthenticatedReturnsFalseIfNoTokenIsAuthenticatedWithStrategyAnyToken()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token1->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token2->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));

        $authenticationTokens = array($token1, $token2);

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationStrategy')->will($this->returnValue(Context::AUTHENTICATE_ANY_TOKEN));
        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue($authenticationTokens));

        $this->assertFalse($this->authenticationProviderManager->isAuthenticated());
    }

    /**
     * @test
     */
    public function isAuthenticatedReturnsTrueIfOneTokenIsAuthenticatedWithStrategyAnyToken()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token1->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token2->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));

        $authenticationTokens = array($token1, $token2);

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationStrategy')->will($this->returnValue(Context::AUTHENTICATE_ANY_TOKEN));
        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue($authenticationTokens));

        $this->assertTrue($this->authenticationProviderManager->isAuthenticated());
    }

    /**
     * @test
     */
    public function logoutReturnsIfNoAccountIsAuthenticated()
    {
        $this->mockSecurityContext->expects($this->never())->method('isInitialized');
        /** @var AuthenticationProviderManager|\PHPUnit_Framework_MockObject_MockObject $authenticationProviderManager */
        $authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('isAuthenticated'), array(), '', false);
        $authenticationProviderManager->expects($this->once())->method('isAuthenticated')->will($this->returnValue(false));
        $authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function logoutSetsTheAuthenticationStatusOfAllActiveAuthenticationTokensToNoCredentialsGiven()
    {
        $token1 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token1->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));
        $token1->expects($this->once())->method('setAuthenticationStatus')->with(TokenInterface::NO_CREDENTIALS_GIVEN);
        $token2 = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token2->expects($this->once())->method('setAuthenticationStatus')->with(TokenInterface::NO_CREDENTIALS_GIVEN);

        $authenticationTokens = array($token1, $token2);

        $this->mockSecurityContext->expects($this->atLeastOnce())->method('getAuthenticationTokens')->will($this->returnValue($authenticationTokens));

        $this->authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function logoutDestroysSessionIfStarted()
    {
        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('emitLoggedOut'), array(), '', false);
        $this->inject($this->authenticationProviderManager, 'securityContext', $this->mockSecurityContext);
        $this->inject($this->authenticationProviderManager, 'session', $this->mockSession);

        $this->mockSession->expects($this->any())->method('canBeResumed')->will($this->returnValue(true));
        $this->mockSession->expects($this->any())->method('isStarted')->will($this->returnValue(true));

        $token = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token->expects($this->any())->method('isAuthenticated')->will($this->returnValue(true));

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationTokens')->will($this->returnValue(array($token)));

        $this->mockSession->expects($this->once())->method('destroy');

        $this->authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function logoutDoesNotDestroySessionIfNotStarted()
    {
        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('emitLoggedOut'), array(), '', false);
        $this->inject($this->authenticationProviderManager, 'securityContext', $this->mockSecurityContext);
        $this->inject($this->authenticationProviderManager, 'session', $this->mockSession);

        $token = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token->expects($this->any())->method('isAuthenticated')->will($this->returnValue(true));

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationTokens')->will($this->returnValue(array($token)));

        $this->mockSession->expects($this->never())->method('destroy');

        $this->authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function logoutEmitsLoggedOutSignalBeforeDestroyingSession()
    {
        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('emitLoggedOut'), array(), '', false);
        $this->inject($this->authenticationProviderManager, 'securityContext', $this->mockSecurityContext);
        $this->inject($this->authenticationProviderManager, 'session', $this->mockSession);

        $this->mockSession->expects($this->any())->method('canBeResumed')->will($this->returnValue(true));
        $this->mockSession->expects($this->any())->method('isStarted')->will($this->returnValue(true));

        $token = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token->expects($this->any())->method('isAuthenticated')->will($this->returnValue(true));

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationTokens')->will($this->returnValue(array($token)));

        $loggedOutEmitted = false;
        $this->authenticationProviderManager->expects($this->once())->method('emitLoggedOut')->will($this->returnCallback(function () use (&$loggedOutEmitted) {
            $loggedOutEmitted = true;
        }));
        $this->mockSession->expects($this->once())->method('destroy')->will($this->returnCallback(function () use (&$loggedOutEmitted) {
            if (!$loggedOutEmitted) {
                \PHPUnit_Framework_Assert::fail('emitLoggedOut was not called before destroy');
            }
        }));

        $this->authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function logoutRefreshesTokensInSecurityContext()
    {
        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('emitLoggedOut'), array(), '', false);
        $this->inject($this->authenticationProviderManager, 'securityContext', $this->mockSecurityContext);
        $this->inject($this->authenticationProviderManager, 'session', $this->mockSession);

        $this->mockSession->expects($this->any())->method('canBeResumed')->will($this->returnValue(true));
        $this->mockSession->expects($this->any())->method('isStarted')->will($this->returnValue(true));

        $token = $this->getMock(\TYPO3\Flow\Security\Authentication\TokenInterface::class, array(), array(), '', false);
        $token->expects($this->any())->method('isAuthenticated')->will($this->returnValue(true));

        $this->mockSecurityContext->expects($this->any())->method('getAuthenticationTokens')->will($this->returnValue(array($token)));

        $this->mockSecurityContext->expects($this->once())->method('refreshTokens');

        $this->authenticationProviderManager->logout();
    }

    /**
     * @test
     */
    public function noTokensAndProvidersAreBuiltIfTheConfigurationArrayIsEmpty()
    {
        $this->authenticationProviderManager->_call('buildProvidersAndTokensFromConfiguration', array());

        $providers = $this->authenticationProviderManager->_get('providers');
        $tokens = $this->authenticationProviderManager->_get('tokens');

        $this->assertEquals(array(), $providers, 'The array of providers should be empty.');
        $this->assertEquals(array(), $tokens, 'The array of tokens should be empty.');
    }

    /**
     * @test
     * @expectedException \TYPO3\Flow\Security\Exception\InvalidAuthenticationProviderException
     */
    public function anExceptionIsThrownIfTheConfiguredProviderDoesNotExist()
    {
        $providerConfiguration = array(
            'NotExistingProvider' => array(
                'providerClass' => 'NotExistingProviderClass'
            ),
        );

        $mockProviderResolver = $this->getMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderResolver::class, array(), array(), '', false);
        $mockRequestPatternResolver = $this->getMock(\TYPO3\Flow\Security\RequestPatternResolver::class, array(), array(), '', false);

        $this->authenticationProviderManager = $this->getAccessibleMock(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, array('authenticate'), array($mockProviderResolver, $mockRequestPatternResolver));
        $this->authenticationProviderManager->_call('buildProvidersAndTokensFromConfiguration', $providerConfiguration);
    }
}
