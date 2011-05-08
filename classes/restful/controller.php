<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Extended Restful controller to add additional support for
 * traditional restful interfaces.
 *
 * @package   Restful
 * @category  Controller
 * @author    Sam de Freyssinet
 */
class Restful_Controller extends Kohana_Controller_REST {

	/**
	 * @var     array
	 */
	protected $_accept;

	/**
	 * @var     array
	 */
	protected $_accept_charset;

	/**
	 * @var     array
	 */
	protected $_accept_language;

	/**
	 * @var     boolean
	 */
	protected $_accept_strict;

	/**
	 * Creates a new controller instance. Each controller must be constructed
	 * with the request object that created it.
	 *
	 * @param   Request   $request  Request that created the controller
	 * @param   Response  $response The request's response
	 * @param   array     $accept
	 * @param   array     $accept_charset
	 * @param   array     $accept_language
	 * @param   boolean   $strict
	 * @return  void
	 */
	public function __construct(Request $request,
	                            Response $response,
	                            array $accept = array(),
	                            array $accept_charset = array(),
	                            array $accept_language = array(),
	                            $accept_strict = FALSE)
	{
		parent::__construct($request, $response);

		$this->accept($accept);
		$this->accept_charset($accept_charset);
		$this->accept_language($accept_language);

		$this->_accept_strict = (bool) $accept_strict;

		if ( ! $this->_accept_strict)
		{
			// Set defaults if required
			( ! $this->accept()) AND $this->accept(array(Kohana::$content_type));
			( ! $this->accept_charset()) AND $this->accept_charset(array(Kohana::$charset));
			( ! $this->accept_language()) AND $this->accept_language(array(I18n::$lang));
		}
	}

	/**
	 * Run before the controller method is executed. If accept_strict
	 * is `TRUE`, this method will validate the incoming request and
	 * ensure it meets the Accept requirements.
	 * 
	 * On failure, either a Http_Exception_406 Not Acceptable or
	 * Http_Exception_405 Method Not Allowed will be thrown.
	 *
	 * @return  void
	 * @throws  Http_Exception_405
	 * @throws  Http_Exception_406
	 */
	public function before()
	{
		parent::before();

		// Check Http Method is supported
		if ($this->response->status() === 405)
			throw new Http_Exception_405('Method :method not allowed.', array(':method' => $this->request->method()));
		else if ($this->_accept_strict)
		{
			$request_header = $this->request->headers();
			$resolved_accept = $request_header->resolve_accept($this, $this->_accept_strict);

			if ($this->accept() AND ! isset($resolved['accept']))
				throw new Http_Exception_406('Supplied accept mimes: :accept not supported. Supported mimes: :mimes',
					array(
						':accept' => (string) $request_header['accept'],
						':mimes'  => implode(', ', $this->accept())
					));

			if ($this->accept_charset() AND ! isset($resolved['accept-charset']))
				throw new Http_Exception_406('Supplied accept character set: :accept_charset not supported. Supported character sets: :charsets',
					array(
						':accept_charset' => (string) $request_header['accept'],
						':charsets'  => implode(', ', $this->accept_charset())
					));

			if ($this->accept_language() AND ! isset($resolved['accept-language']))
				throw new Http_Exception_406('Supplied accept languages: :accept_language not supported. Supported languages: :langauges',
					array(
						':accept_language' => (string) $request_header['accept'],
						':languages'  => implode(', ', $this->accept_language())
					));
		}
	}

	/**
	 * Gets and sets the accept mime types for this
	 * controller.
	 *
	 * @param   array         value(s) to set
	 * @return  Restful_Controller
	 * @return  array
	 */
	public function accept(array $value = NULL)
	{
		if ($value === NULL)
			return $this->_accept;

		$this->_accept = array_map('strtolower', $value);
		return $this;
	}

	/**
	 * Gets and sets the accept character sets for this
	 * controller.
	 *
	 * @param   array         value(s) to set
	 * @return  Restful_Controller
	 * @return  array
	 */
	public function accept_charset(array $value = NULL)
	{
		if ($value === NULL)
			return $this->_accept_charset;

		$this->_accept_charset = array_map('strtolower', $value);
		return $this;
	}

	/**
	 * Gets and sets the accept character sets for this
	 * controller.
	 *
	 * @param   array         value(s) to set
	 * @return  Restful_Controller
	 * @return  array
	 */
	public function accept_language(array $value = NULL)
	{
		if ($value === NULL)
			return $this->_accept_language;

		$this->_accept_language = array_map('strtolower', $value);
		return $this;
	}
}