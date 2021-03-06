<?php
/**
 * @package     Tests.Unit
 * @subpackage  Element
 *
 * @copyright   Copyright (C) 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for PNPlace.
 *
 * @package     Tests.Unit
 * @subpackage  Element
 * @since       1.0
 */
class PNPlaceTest extends TestCase
{
	/**
	 * @var    PNPlace  A PNPlace instance.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Setup.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new PNPlace;

		// Mock the token set.
		$tokenSetMock = $this->getMock('PNTokenSet');
		TestReflection::setValue($this->object, 'tokenSet', $tokenSetMock);
	}

	/**
	 * Constructor.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		$place = new PNPlace;
		$this->assertInstanceOf('PNTokenSet', TestReflection::getValue($place, 'tokenSet'));

		// Just need to check the token set.
		$tokenSet = new PNTokenSet;
		$place = new PNPlace(null, $tokenSet);
		$this->assertEquals($tokenSet, TestReflection::getValue($place, 'tokenSet'));
	}

	/**
	 * Check if the place is loaded.
	 * To be loaded it must have at least one input or output.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::isLoaded
	 * @since   1.0
	 */
	public function testIsLoaded()
	{
		$this->assertFalse($this->object->isLoaded());

		// Add an input.
		TestReflection::setValue($this->object, 'inputs', array('test'));

		$this->assertTrue($this->object->isLoaded());

		// Reset the inputs.
		TestReflection::setValue($this->object, 'inputs', array());

		// Add an output.
		TestReflection::setValue($this->object, 'outputs', array('test'));

		$this->assertTrue($this->object->isLoaded());
	}

	/**
	 * Check if the given token can be added to this place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::isAllowed
	 * @since   1.0
	 */
	public function testIsAllowed()
	{
		// Test non colored mode.
		$this->assertTrue($this->object->isAllowed(new PNToken));

		// Test colored mode.
		$colorSet = new PNColorSet(array('integer', 'double', 'double'));
		TestReflection::setValue($this->object, 'colorSet', $colorSet);

		// Try with an allowed token.
		$color = new PNColor(array(1, 1.2, 2.2));
		$token = new PNToken($color);

		$this->assertTrue($this->object->isAllowed($token));

		// Try with a not allowed token.
		$color = new PNColor(array(1, '1.2', 2.2));
		$token = new PNToken($color);

		$this->assertFalse($this->object->isAllowed($token));
	}

	/**
	 * Add a Token in this Place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::addTokenWithoutCheck
	 * @since   1.0
	 */
	public function testAddTokenWithoutCheck()
	{
		// Get the mocked tokenset.
		$mock = TestReflection::getValue($this->object, 'tokenSet');

		$mock->expects($this->once())
			->method('addToken');

		$this->object->addToken(new PNToken);
	}

	/**
	 * Add a Token in this Place, only if it is allowed.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::addToken
	 * @since   1.0
	 */
	public function testAddToken()
	{
		// Test with an allowed token.
		$color = new PNColor(array(1, 'test'));
		$token = new PNToken($color);

		$colorSet = new PNColorSet(array('integer', 'string'));
		TestReflection::setValue($this->object, 'colorSet', $colorSet);

		$this->assertTrue($this->object->addToken($token));

		// Test with non allowed token.
		$color = new PNColor(array(1, 1));
		$token = new PNToken($color);

		$this->assertFalse($this->object->addToken($token));
	}

	/**
	 * Add multiple Tokens in this Place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::addTokens
	 * @since   1.0
	 */
	public function testAddTokens()
	{
		// Set the token set.
		TestReflection::setValue($this->object, 'tokenSet', new PNTokenSet);

		// Test with a allowed tokens.
		$color1 = new PNColor(array(1, 'test'));
		$token1 = new PNToken($color1);

		$color2 = new PNColor(array(22, 'hello'));
		$token2 = new PNToken($color2);

		// Set the place color set.
		$colorSet = new PNColorSet(array('integer', 'string'));
		TestReflection::setValue($this->object, 'colorSet', $colorSet);

		// Add the tokens.
		$this->object->addTokens(array($token1, $token2));

		$tokenSet = TestReflection::getValue($this->object, 'tokenSet');
		$tokens = $tokenSet->getTokens();

		$tokens = array_values($tokens);

		$this->assertContains($token1, $tokens[0]);
		$this->assertContains($token2, $tokens[1]);

		// Reset the tokenSet.
		TestReflection::setValue($this->object, 'tokenSet', new PNTokenSet);

		// Test with non allowed tokens.
		$color1 = new PNColor(array('test', 'test'));
		$token1 = new PNToken($color1);

		$color2 = new PNColor(array(array(3), 'hello'));
		$token2 = new PNToken($color2);

		// Add the tokens.
		$this->object->addTokens(array($token1, $token2));

		$tokenSet = TestReflection::getValue($this->object, 'tokenSet');
		$tokens = $tokenSet->getTokens();

		$this->assertEmpty($tokens);
	}

	/**
	 * Remove a Token from this Place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::removeToken
	 * @since   1.0
	 */
	public function testRemoveToken()
	{
		// Get the mocked tokenset.
		$mock = TestReflection::getValue($this->object, 'tokenSet');

		$mock->expects($this->once())
			->method('removeToken');

		$this->object->removeToken(new PNToken);
	}

	/**
	 * Remove all the Tokens from this Place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::clearTokens
	 * @since   1.0
	 */
	public function testClearTokens()
	{
		// Get the mocked tokenset.
		$mock = TestReflection::getValue($this->object, 'tokenSet');

		$mock->expects($this->once())
			->method('clear');

		$this->object->clearTokens();
	}

	/**
	 * Get the Tokens in this Place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::getTokens
	 * @since   1.0
	 */
	public function testGetTokens()
	{
		// Get the mocked tokenset.
		$mock = TestReflection::getValue($this->object, 'tokenSet');

		$mock->expects($this->once())
			->method('getTokens');

		$this->object->getTokens();
	}

	/**
	 * Get the number of tokens in this place.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::getTokenCount
	 * @since   1.0
	 */
	public function testGetTokenCount()
	{
		// Get the mocked tokenset.
		$mock = TestReflection::getValue($this->object, 'tokenSet');

		$mock->expects($this->once())
			->method('count');

		$this->object->getTokenCount();
	}

	/**
	 * Check if the place is a Start Place.
	 * It means there are no input(s).
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::isStart
	 * @since   1.0
	 */
	public function testIsStart()
	{
		$this->assertTrue($this->object->isStart());

		TestReflection::setValue($this->object, 'inputs', array(1));

		$this->assertFalse($this->object->isStart());
	}

	/**
	 * Check if the place is a End Place.
	 * It means there are no ouput(s).
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::isEnd
	 * @since   1.0
	 */
	public function testIsEnd()
	{
		$this->assertTrue($this->object->isEnd());

		TestReflection::setValue($this->object, 'outputs', array(1));

		$this->assertFalse($this->object->isEnd());
	}

	/**
	 * Accept the Visitor.
	 *
	 * @return  void
	 *
	 * @covers  PNPlace::accept
	 * @since   1.0
	 */
	public function testAccept()
	{
		$visitor = $this->getMockForAbstractClass('PNBaseVisitor', array(), '', true, true, true, array('visitPlace'));

		// Create two mocked input arcs.
		$arc1 = $this->getMock('PNArc');
		$arc1->expects($this->once())
			->method('accept')
			->with($visitor);

		$arc2 = $this->getMock('PNArc');
		$arc2->expects($this->once())
			->method('accept')
			->with($visitor);

		// Inject them.
		TestReflection::setValue($this->object, 'outputs', array($arc1, $arc2));

		$visitor->expects($this->once())
			->method('visitPlace')
			->with($this->object);

		$this->object->accept($visitor);
	}
}
