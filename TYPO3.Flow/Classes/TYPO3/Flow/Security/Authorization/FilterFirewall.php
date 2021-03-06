<?php
namespace TYPO3\Flow\Security\Authorization;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Default Firewall which analyzes the request with a RequestFilter chain.
 *
 * @Flow\Scope("singleton")
 */
class FilterFirewall implements \TYPO3\Flow\Security\Authorization\FirewallInterface
{
    /**
     * @var \TYPO3\Flow\Object\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var \TYPO3\Flow\Security\RequestPatternResolver
     */
    protected $requestPatternResolver = null;

    /**
     * @var \TYPO3\Flow\Security\Authorization\InterceptorResolver
     */
    protected $interceptorResolver = null;

    /**
     * @var array of \TYPO3\Flow\Security\Authorization\RequestFilter instances
     */
    protected $filters = array();

    /**
     * If set to TRUE the firewall will reject any request except the ones explicitly
     * whitelisted by a \TYPO3\Flow\Security\Authorization\AccessGrantInterceptor
     * @var boolean
     */
    protected $rejectAll = false;

    /**
     * Constructor.
     *
     * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager The object manager
     * @param \TYPO3\Flow\Security\RequestPatternResolver $requestPatternResolver The request pattern resolver
     * @param \TYPO3\Flow\Security\Authorization\InterceptorResolver $interceptorResolver The interceptor resolver
     */
    public function __construct(\TYPO3\Flow\Object\ObjectManagerInterface $objectManager,
            \TYPO3\Flow\Security\RequestPatternResolver $requestPatternResolver,
            \TYPO3\Flow\Security\Authorization\InterceptorResolver $interceptorResolver)
    {
        $this->objectManager = $objectManager;
        $this->requestPatternResolver = $requestPatternResolver;
        $this->interceptorResolver = $interceptorResolver;
    }

    /**
     * Injects the configuration settings
     *
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->rejectAll = $settings['security']['firewall']['rejectAll'];
        $this->buildFiltersFromSettings($settings['security']['firewall']['filters']);
    }

    /**
     * Analyzes a request against the configured firewall rules and blocks
     * any illegal request.
     *
     * @param \TYPO3\Flow\Mvc\ActionRequest $request The request to be analyzed
     * @return void
     * @throws \TYPO3\Flow\Security\Exception\AccessDeniedException if the
     */
    public function blockIllegalRequests(\TYPO3\Flow\Mvc\ActionRequest $request)
    {
        $filterMatched = false;
        /** @var $filter \TYPO3\Flow\Security\Authorization\RequestFilter */
        foreach ($this->filters as $filter) {
            if ($filter->filterRequest($request)) {
                $filterMatched = true;
            }
        }
        if ($this->rejectAll && !$filterMatched) {
            throw new \TYPO3\Flow\Security\Exception\AccessDeniedException('The request was blocked, because no request filter explicitly allowed it.', 1216923741);
        }
    }

    /**
     * Sets the internal filters based on the given configuration.
     *
     * @param array $filterSettings The filter settings
     * @return void
     */
    protected function buildFiltersFromSettings(array $filterSettings)
    {
        foreach ($filterSettings as $singleFilterSettings) {
            /** @var $requestPattern \TYPO3\Flow\Security\RequestPatternInterface */
            $requestPattern = $this->objectManager->get($this->requestPatternResolver->resolveRequestPatternClass($singleFilterSettings['patternType']));
            $requestPattern->setPattern($singleFilterSettings['patternValue']);
            $interceptor = $this->objectManager->get($this->interceptorResolver->resolveInterceptorClass($singleFilterSettings['interceptor']));

            $this->filters[] = $this->objectManager->get(\TYPO3\Flow\Security\Authorization\RequestFilter::class, $requestPattern, $interceptor);
        }
    }
}
