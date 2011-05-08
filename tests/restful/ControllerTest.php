<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test Suite for the [Restful_Controller] class.
 *
 * @package  Restful
 * @category Controller Test
 * @author   Sam de Freyssinet
 * @uses     [Unittest_TestCase]
 */
class Restful_ControllerTest extends Unittest_TestCase {

	/**
	 * Provides data for test_accept_set
	 *
	 * @return  array
	 */
	public function provider_accept_set()
	{
		return array(
			array(
				'text/html',
				NULL
			),
			array(
				array('application/xml', 'text/html'),
				array('application/xml', 'text/html')
			),
			array(
				array('Application/XML', 'text/html'),
				array('application/xml', 'text/html')
			),
		);
	}

	/**
	 * Tests the [Restful_Controller::accept()] method
	 * 
	 * @dataProvider  provider_accept_set
	 *
	 * @param   array    value 
	 * @param   array    expected 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_set($value, $expected)
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		if ( ! is_array($value))
		{
			$this->setExpectedException('Exception');
		}

		$this->assertSame($controller, $controller->accept($value));
		$this->assertSame($expected, $controller->accept());
	}

	/**
	 * Tests the [Restful_Controller::accept()] method
	 * 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_get()
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		$this->assertTrue(is_array($controller->accept()));
		$this->assertSame(array('text/html'), $controller->accept());
	}

	/**
	 * Provides data for test_accept_langauge_set
	 *
	 * @return  array
	 */
	public function provider_accept_language_set()
	{
		return array(
			array(
				'en-GB',
				NULL
			),
			array(
				array('en-gb', 'en-us'),
				array('en-gb', 'en-us')
			),
			array(
				array('en-GB', 'en-US'),
				array('en-gb', 'en-us')
			),
		);
	}

	/**
	 * Tests the [Restful_Controller::accept_langauge()] method
	 * 
	 * @dataProvider  provider_accept_language_set
	 *
	 * @param   array    value 
	 * @param   array    expected 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_language_set($value, $expected)
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		if ( ! is_array($value))
		{
			$this->setExpectedException('Exception');
		}

		$this->assertSame($controller, $controller->accept_language($value));
		$this->assertSame($expected, $controller->accept_language());
	}

	/**
	 * Tests the [Restful_Controller::accept_language()] method
	 * 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_langauge_get()
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		$this->assertTrue(is_array($controller->accept_language()));
		$this->assertSame(array('en-gb'), $controller->accept_language());
	}

	/**
	 * Provides data for test_accept_charset_set
	 *
	 * @return  array
	 */
	public function provider_accept_charset_set()
	{
		return array(
			array(
				'utf-8',
				NULL
			),
			array(
				array('utf-8', 'utf-16'),
				array('utf-8', 'utf-16')
			),
			array(
				array('UTF-8', 'UTF-16'),
				array('utf-8', 'utf-16')
			),
		);
	}

	/**
	 * Tests the [Restful_Controller::accept_charset()] method
	 * 
	 * @dataProvider  provider_accept_charset_set
	 *
	 * @param   array    value 
	 * @param   array    expected 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_charset_set($value, $expected)
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		if ( ! is_array($value))
		{
			$this->setExpectedException('Exception');
		}

		$this->assertSame($controller, $controller->accept_charset($value));
		$this->assertSame($expected, $controller->accept_charset());
	}

	/**
	 * Tests the [Restful_Controller::accept_charset()] method
	 * 
	 * @return  void
	 * @author  Sam de Freyssinet
	 */
	public function test_accept_charset_get()
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		$this->assertTrue(is_array($controller->accept_charset()));
		$this->assertSame(array('utf-8'), $controller->accept_charset());
	}

	/**
	 * Provides data for test_resolve_accept
	 *
	 * @return  array
	 */
	public function provider_resolve_accept()
	{
		return array(
			array(
				new Http_Header(array(
					'accept'          => 'application/xml, text/html'
				)),
				array('accept' => array(
						'text/html'
				)),
				array(
					'accept'          => 'text/html',
					'accept-language' => I18n::$lang,
					'accept-charset'  => Kohana::$charset
				),
				FALSE
			),
			array(
				new Http_Header(array(
					'accept'          => 'application/json, application/xml; q=0.9, text/html; q=0.5',
					'accept-language' => 'en-GB, en-US; q=0.9, en; q=0.5',
					'accept-charset'  => 'ISO-8859-1, Utf-8; q=0.9, utf-10'
				)),
				array(
					'accept' => array(
						'text/html', 'application/xml'
					),
					'accept_language' => array(
						'en-gb',
						'en',
					),
					'accept_charset'  => array(
						'utf-8',
						'utf-10'
					)
				),
				array(
					'accept'          => 'application/xml',
					'accept-language' => 'en-gb',
					'accept-charset'  => 'utf-10',
				),
				FALSE
			),
			array(
				new Http_Header(array(
					'accept'          => 'application/atom+xml, text/html; q=0.9, application/json; q=0.8'
				)),
				array('accept' => array(
						'application/xml',
						'application/json'
				)),
				array(
					'accept'          => 'application/json',
				),
				TRUE
			),
		);
	}

	/**
	 * Tests [Restful Controller::resolve_accept()] works as
	 * designed
	 * 
	 * @dataProvider provider_resolve_accept
	 *
	 * @param   Http_Header  header 
	 * @param   array    accept properties
	 * @param   array
	 * @param   boolean  strict testing?
	 * @return  void
	 */
	public function test_resolve_accept($header, $accept, $expected, $strict)
	{
		// Setup the controller
		$request = new Request('foo/bar');
		$controller = new Restful_Controller($request, $request->create_response());

		foreach($accept as $method => $value)
		{
			$controller->$method($value);
		}

		$result = $controller->resolve_accept($header, $strict);

		foreach ($expected as $k => $v)
		{
			$this->assertSame($expected[$k], $result[$k]);
		}

		if ($strict)
		{
			$this->assertSame($expected, $result);
		}
	}

	/**
	 * Data provider for test_not_acceptable_request
	 *
	 * @return  array
	 */
	public function provider_not_acceptable_request()
	{
		return array(
			array(
				array(
					'accept'          => 'application/xml',
					'accept-charset'  => 'utf-10',
					'accept-language' => 'en-GB'
				),
				array(
					'accept'          => array('text/html'),
					'accept-charset'  => array('utf-8'),
					'accept-language' => array('en-US'),
				)
			),
			array(
				array(
					'accept'          => 'application/xml',
					'accept-charset'  => 'utf-10',
					'accept-language' => 'en-GB'
				),
				array(
					'accept'          => array('text/html', 'application/xml'),
					'accept-charset'  => array('utf-8'),
					'accept-language' => array('en-US'),
				)
			),
			array(
				array(
					'accept'          => 'application/xml',
					'accept-charset'  => 'utf-10',
					'accept-language' => 'en-GB'
				),
				array(
					'accept'          => array('text/html', 'application/xml'),
					'accept-charset'  => array('utf-8', 'utf-10'),
					'accept-language' => array('en-US'),
				)
			)
		);
	}

	/**
	 * Tests whether strict typing throws the correct exception
	 * if Not Acceptable.
	 * 
	 * @dataProvider provider_not_acceptable_request
	 * @expectedException Http_Exception_406
	 * 
	 * @return void
	 */
	public function test_not_acceptable_request(array $headers, array $acceptable)
	{
		// Setup the controller
		$request = new Request('foo/bar');

		$request->headers($headers);

		$controller = new Restful_Controller($request,
			$request->create_response(),
			$acceptable['accept'],
			$acceptable['accept-charset'],
			$acceptable['accept-language'],
			TRUE);

		$controller->before();
	}
}