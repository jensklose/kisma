<?php
require_once '/media/storage/mounts/projects/php/kisma/framework/Kisma.php';
require_once '/media/storage/mounts/projects/php/kisma/framework/components/Component.php';

/**
 * Test class for Component.
 * Generated by PHPUnit on 2011-09-19 at 21:55:10.
 */
class ComponentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Kisma\Components\Component
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
		$_options = array(
			'readOnly' => false,
			'logging' => false,
			'eventHandlerSignature' => 'onTest',
			'bogusProperty' => 'sadness',
		);

        $this->object = new \Kisma\Components\Component( $_options );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement test__call().
     */
    public function test__call()
    {
    }

    /**
     * @todo Implement testBindEvents().
     */
    public function testBindEvents()
    {
    }

    /**
     * @todo Implement testBind().
     */
    public function testBind()
    {
    }

    /**
     * @todo Implement testUnbind().
     */
    public function testUnbind()
    {
    }

    /**
     * @todo Implement testTrigger().
     */
    public function testTrigger()
    {
    }

    /**
     * @todo Implement testLinkAspect().
     */
    public function testLinkAspect()
    {
    }

    /**
     * @todo Implement testLinkAspects().
     */
    public function testLinkAspects()
    {
    }

    /**
     * @todo Implement testUnlinkAspects().
     */
    public function testUnlinkAspects()
    {
    }

    /**
     * @todo Implement testUnlinkAspect().
     */
    public function testUnlinkAspect()
    {
    }

    /**
     * @todo Implement testLinkHelpers().
     */
    public function testLinkHelpers()
    {
    }

    /**
     * @todo Implement testLinkHelper().
     */
    public function testLinkHelper()
    {
    }

    /**
     * @todo Implement testUnlinkHelpers().
     */
    public function testUnlinkHelpers()
    {
    }

    /**
     * @todo Implement testUnlinkHelper().
     */
    public function testUnlinkHelper()
    {
    }

    /**
     * @todo Implement testCount().
     */
    public function testCount()
    {
    }

    /**
     * @todo Implement testCurrent().
     */
    public function testCurrent()
    {
    }

    /**
     * @todo Implement testKey().
     */
    public function testKey()
    {
    }

    /**
     * @todo Implement testNext().
     */
    public function testNext()
    {
    }

    /**
     * @todo Implement testRewind().
     */
    public function testRewind()
    {
    }

    /**
     * @todo Implement testValid().
     */
    public function testValid()
    {
    }

    /**
     * @todo Implement testSetAspects().
     */
    public function testSetAspects()
    {
    }

    /**
     * @todo Implement testGetAspects().
     */
    public function testGetAspects()
    {
    }

    /**
     * @todo Implement testSetErrors().
     */
    public function testSetErrors()
    {
    }

    /**
     * @todo Implement testGetErrors().
     */
    public function testGetErrors()
    {
    }

    /**
     * @todo Implement testSetEvents().
     */
    public function testSetEvents()
    {
    }

    /**
     * @todo Implement testGetEvents().
     */
    public function testGetEvents()
    {
    }

    /**
     * @todo Implement testSetIndex().
     */
    public function testSetIndex()
    {
    }

    /**
     * @todo Implement testGetIndex().
     */
    public function testGetIndex()
    {
    }

    /**
     * @todo Implement testSetLogging().
     */
    public function testSetLogging()
    {
    }

    /**
     * @todo Implement testGetLogging().
     */
    public function testGetLogging()
    {
    }

    /**
     * @todo Implement testGetCount().
     */
    public function testGetCount()
    {
    }

    /**
     * @todo Implement testSetOptions().
     */
    public function testSetOptions()
    {
    }

    /**
     * @todo Implement testGetOptions().
     */
    public function testGetOptions()
    {
    }

    /**
     * @todo Implement testSetReadOnly().
     */
    public function testSetReadOnly()
    {
    }

    /**
     * @todo Implement testGetReadOnly().
     */
    public function testGetReadOnly()
    {
    }

    /**
     * @todo Implement testSetSkipNext().
     */
    public function testSetSkipNext()
    {
    }

    /**
     * @todo Implement testGetSkipNext().
     */
    public function testGetSkipNext()
    {
    }

    /**
     * @todo Implement testSetEventHandlerSignature().
     */
    public function testSetEventHandlerSignature()
    {
    }

    /**
     * @todo Implement testGetEventHandlerSignature().
     */
    public function testGetEventHandlerSignature()
    {
    }
}
?>
