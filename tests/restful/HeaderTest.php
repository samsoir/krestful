<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test Suite for the [Restful_Controller] class.
 *
 * @package  Restful
 * @category HTTP Header Test
 * @author   Sam de Freyssinet
 * @uses     [Unittest_TestCase]
 */
class Restful_HeaderTest extends Unittest_TestCase {

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

		$result = $request->headers()->resolve_accept($controller, $strict);

		foreach ($expected as $k => $v)
		{
			$this->assertSame($expected[$k], $result[$k]);
		}

		if ($strict)
		{
			$this->assertSame($expected, $result);
		}
	}

} // End Restful_HeaderTest