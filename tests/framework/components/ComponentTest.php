<?php
require_once __DIR__ . '/../../../src/Kisma.php';
use Kisma\Components as Components;

/**
 *
 */
class TestComponent extends \Kisma\Components\Component
{
}

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
			'bogusProperty' => 'sadness',
			'aspect.options' => array(
				'classes' => array(
					'kisma.aspects.storage.couch_db',
				),
			),
		);

        $this->object = new TestComponent( $_options );
    }

    /**
     * @todo Implement test__call().
     */
    public function test__call()
    {
		/** @var $_couch \Kisma\Aspects\Storage\CouchDb */
		$_couch = $this->object->{'kisma.aspects.storage.couch_db'};

		try
		{
			$_couch->createDatabase( 'gha-test' );
		}
		catch ( \SagCouchException $_ex )
		{
			//	Already there...
		}

		$_o->
    }
	
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

}
