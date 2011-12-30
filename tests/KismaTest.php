<?php
namespace Kisma;

require_once __DIR__ . '/../src/Kisma.php';

/**
 * Test class for Kisma.
 * Generated by PHPUnit on 2011-12-29 at 23:36:24.
 */
class KismaTest extends TestCase
{
	/**
	 * @var Kisma
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Kisma();

		$this->object->register(
			new Provider\CouchDbServiceProvider(),
			array(
				'couchdb.options' => array(
					'dbname' => '_users',
					'host' => 'teledini.gna.me',
					'user' => 'sinker',
					'password' => 'sinker',
				),
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testInitialize()
	{
		$_databases = $this->object['couchdb.client']->allDocs();
		$this->assertNull( $_databases, 'All databases returned null: ' . print_r( $_databases, true ) );
	}

//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testTerminate().
//	 */
//	public function testTerminate()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testGetOption().
//	 */
//	public function testGetOption()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testO().
//	 */
//	public function testO()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testOo().
//	 */
//	public function testOo()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testSetOption().
//	 */
//	public function testSetOption()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testSo().
//	 */
//	public function testSo()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testUnsetOption().
//	 */
//	public function testUnsetOption()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
//
//	/**
//	 * @covers {className}::{origMethodName}
//	 * @todo Implement testUo().
//	 */
//	public function testUo()
//	{
//		// Remove the following lines when you implement this test.
//		$this->markTestSkipped( 'This test has not been implemented yet.' );
//	}
}