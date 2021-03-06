<?php
namespace TYPO3\Flow\Security\Authorization\Privilege\Entity\Doctrine;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter as DoctrineSqlFilter;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Security\Context;
use TYPO3\Flow\Security\Policy\PolicyService;
use TYPO3\Flow\Security\Authorization\Privilege\Entity\EntityPrivilegeInterface;

/**
 * A filter to rewrite doctrine queries according to the security policy.
 *
 * @Flow\Proxy(false)
 */
class SqlFilter extends DoctrineSqlFilter
{
    /**
     * @var PolicyService
     */
    protected $policyService;

    /**
     * @var Context
     */
    protected $securityContext;

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity Metadata object for the target entity to be filtered
     * @param string $targetTableAlias The target table alias used in the current query
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $this->initializeDependencies();

        /*
         * TODO: Instead of checking for class account we could introduce some interface for white listing entities from entity security checks
         * Problem with checking the Account is, that this filter calls getRoles() on the security context while accounts are not
         * yet fully initialized. By this we get a half built account object that will end up in access denied exception,
         * as it has no roles (and other properties) set
         */
        if ($this->securityContext->areAuthorizationChecksDisabled() || $targetEntity->getName() === \TYPO3\Flow\Security\Account::class) {
            return '';
        }
        if (!$this->securityContext->isInitialized()) {
            if (!$this->securityContext->canBeInitialized()) {
                return '';
            }
            $this->securityContext->initialize();
        }

        // This is needed to include the current context of roles into query cache identifier
        $this->setParameter('__contextHash', $this->securityContext->getContextHash(), 'string');

        $sqlConstraints = array();
        $grantedConstraints = array();
        $deniedConstraints = array();
        foreach ($this->securityContext->getRoles() as $role) {
            $entityPrivileges = $role->getPrivilegesByType(\TYPO3\Flow\Security\Authorization\Privilege\Entity\EntityPrivilegeInterface::class);
            /** @var EntityPrivilegeInterface $privilege */
            foreach ($entityPrivileges as $privilege) {
                if (!$privilege->matchesEntityType($targetEntity->getName())) {
                    continue;
                }
                $sqlConstraint = $privilege->getSqlConstraint($targetEntity, $targetTableAlias);
                if ($sqlConstraint === null) {
                    continue;
                }

                $sqlConstraints[] = ' NOT (' . $sqlConstraint . ')';
                if ($privilege->isGranted()) {
                    $grantedConstraints[] = ' NOT (' . $sqlConstraint . ')';
                } elseif ($privilege->isDenied()) {
                    $deniedConstraints[] = ' NOT (' . $sqlConstraint . ')';
                }
            }
        }

        $grantedConstraints = array_diff($grantedConstraints, $deniedConstraints);
        $effectiveConstraints = array_diff($sqlConstraints, $grantedConstraints);

        if (count($effectiveConstraints) > 0) {
            return ' (' . implode(') AND (', $effectiveConstraints) . ') ';
        }
        return '';
    }

    /**
     * Initializes the dependencies by retrieving them from the object manager
     *
     * @return void
     */
    protected function initializeDependencies()
    {
        if ($this->securityContext === null) {
            $this->securityContext = Bootstrap::$staticObjectManager->get(\TYPO3\Flow\Security\Context::class);
        }

        if ($this->policyService === null) {
            $this->policyService = Bootstrap::$staticObjectManager->get(\TYPO3\Flow\Security\Policy\PolicyService::class);
        }
    }
}
