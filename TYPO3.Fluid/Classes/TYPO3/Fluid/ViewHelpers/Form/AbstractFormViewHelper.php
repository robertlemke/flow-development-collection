<?php
namespace TYPO3\Fluid\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Abstract Form View Helper. Bundles functionality related to direct property access of objects in other Form ViewHelpers.
 *
 * If you set the "property" attribute to the name of the property to resolve from the object, this class will
 * automatically set the name and value of a form element.
 */
abstract class AbstractFormViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * Injects the Flow Persistence Manager
     *
     * @param PersistenceManagerInterface $persistenceManager
     * @return void
     */
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Prefixes / namespaces the given name with the form field prefix
     *
     * @param string $fieldName field name to be prefixed
     * @return string namespaced field name
     */
    protected function prefixFieldName($fieldName)
    {
        if ($fieldName === null || $fieldName === '') {
            return '';
        }
        if (!$this->viewHelperVariableContainer->exists(\TYPO3\Fluid\ViewHelpers\FormViewHelper::class, 'fieldNamePrefix')) {
            return $fieldName;
        }
        $fieldNamePrefix = (string)$this->viewHelperVariableContainer->get(\TYPO3\Fluid\ViewHelpers\FormViewHelper::class, 'fieldNamePrefix');
        if ($fieldNamePrefix === '') {
            return $fieldName;
        }
        $fieldNameSegments = explode('[', $fieldName, 2);
        $fieldName = $fieldNamePrefix . '[' . $fieldNameSegments[0] . ']';
        if (count($fieldNameSegments) > 1) {
            $fieldName .= '[' . $fieldNameSegments[1];
        }
        return $fieldName;
    }

    /**
     * Renders a hidden form field containing the technical identity of the given object.
     *
     * @param object $object Object to create the identity field for
     * @param string $name Name
     * @return string A hidden field containing the Identity (UUID in Flow) of the given object or NULL if the object is unknown to the persistence framework
     * @see \TYPO3\Flow\Mvc\Controller\Argument::setValue()
     */
    protected function renderHiddenIdentityField($object, $name)
    {
        if (!is_object($object) || $this->persistenceManager->isNewObject($object)) {
            return '';
        }
        $identifier = $this->persistenceManager->getIdentifierByObject($object);
        if ($identifier === null) {
            return chr(10) . '<!-- Object of type ' . get_class($object) . ' is without identity -->' . chr(10);
        }
        $name = $this->prefixFieldName($name) . '[__identity]';
        $this->registerFieldNameForFormTokenGeneration($name);

        return chr(10) . '<input type="hidden" name="' . $name . '" value="' . $identifier . '" />' . chr(10);
    }

    /**
     * Register a field name for inclusion in the HMAC / Form Token generation
     *
     * @param string $fieldName name of the field to register
     * @return void
     */
    protected function registerFieldNameForFormTokenGeneration($fieldName)
    {
        if ($this->viewHelperVariableContainer->exists(\TYPO3\Fluid\ViewHelpers\FormViewHelper::class, 'formFieldNames')) {
            $formFieldNames = $this->viewHelperVariableContainer->get(\TYPO3\Fluid\ViewHelpers\FormViewHelper::class, 'formFieldNames');
        } else {
            $formFieldNames = array();
        }
        $formFieldNames[] = $fieldName;
        $this->viewHelperVariableContainer->addOrUpdate(\TYPO3\Fluid\ViewHelpers\FormViewHelper::class, 'formFieldNames', $formFieldNames);
    }
}
