<?php
namespace TYPO3\Flow\Tests\Functional\Mvc\ViewsConfiguration;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Functional tests for the ActionController
 */
class ViewsConfigurationTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @var boolean
     */
    protected $testableHttpEnabled = true;

    /**
     * @var boolean
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * Additional setup: Routes
     */
    public function setUp()
    {
        parent::setUp();

        $this->registerRoute('viewsconfigurationa', 'test/mvc/viewsconfigurationa(/{@action})', array(
            '@package' => 'TYPO3.Flow',
            '@subpackage' => 'Tests\Functional\Mvc\ViewsConfiguration\Fixtures',
            '@controller' => 'ViewsConfigurationTestA',
            '@format' => 'html'
        ));

        $this->registerRoute('viewsconfigurationb', 'test/mvc/viewsconfigurationb(/{@action})', array(
            '@package' => 'TYPO3.Flow',
            '@subpackage' => 'Tests\Functional\Mvc\ViewsConfiguration\Fixtures',
            '@controller' => 'ViewsConfigurationTestB',
            '@action' => 'first',
            '@format' => 'html'
        ));

        $this->registerRoute('viewsconfigurationc', 'test/mvc/viewsconfigurationc(/{@action})', array(
            '@package' => 'TYPO3.Flow',
            '@subpackage' => 'Tests\Functional\Mvc\ViewsConfiguration\Fixtures',
            '@controller' => 'ViewsConfigurationTestC',
            '@action' => 'index',
            '@format' => 'html'
        ));
    }

    /**
     *
     *
     * @test
     */
    public function templatePathAndFilenameIsChanged()
    {
        $response = $this->browser->request('http://localhost/test/mvc/viewsconfigurationa/first');
        $this->assertEquals('Changed on Package Level', $response->getContent());
        $response = $this->browser->request('http://localhost/test/mvc/viewsconfigurationb/first');
        $this->assertEquals('Changed on Controller Level', $response->getContent());
    }

    /**
     *
     *
     * @test
     */
    public function viewObjectNameChanged()
    {
        $response = $this->browser->request('http://localhost/test/mvc/viewsconfigurationc/index');
        $this->assertEquals(\TYPO3\Flow\Tests\Functional\Mvc\ViewsConfiguration\Fixtures\TemplateView::class, $response->getContent());
    }

    /**
     * @test
     */
    public function changeTemplatePathAndFilenameForWidget()
    {
        $response = $this->browser->request('http://localhost/test/mvc/viewsconfigurationa/widget');
        $this->assertEquals('Changed on Package Level', $response->getContent());
    }
}
