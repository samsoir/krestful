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
	public function __construct(
		Request $request,
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
			throw new Http_Exception_405('Method :method not allowed.', array(':method' => $this->request->method()), 405);
		else if ($this->_accept_strict)
		{
			$resolved_accept = $this->resolve_accept($this->request->headers(), $this->_accept_strict);

			if ($this->accept() AND ! isset($resolved['accept']))
				throw new Http_Exception_406('Supplied accept mimes: :accept not supported. Supported mimes: :mimes',
					array(
						':accept' => (string) $this->request->headers('accept'),
						':mimes'  => implode(', ', $this->accept())
					),
					406);

			if ($this->accept_charset() AND ! isset($resolved['accept-charset']))
				throw new Http_Exception_406('Supplied accept character set: :accept_charset not supported. Supported character sets: :charsets',
					array(
						':accept_charset' => (string) $this->request->headers('accept'),
						':charsets'  => implode(', ', $this->accept_charset())
					),
					406);

			if ($this->accept_language() AND ! isset($resolved['accept-language']))
				throw new Http_Exception_406('Supplied accept languages: :accept_language not supported. Supported languages: :langauges',
					array(
						':accept_language' => (string) $this->request->headers('accept'),
						':languages'  => implode(', ', $this->accept_langauge())
					),
					406);
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

	/**
	 * Resolves the HTTP accept(-*) headers providing the
	 * matched/default entries for
	 * 
	 *  - Accept
	 *  - Accept-Charset
	 *  - Accept-Language
	 * 
	 * Resolutions are matched against the supplied `$header`,
	 * or the [Restful_Controller::$request] object if none
	 * supplied.
	 * 
	 * Unresolved entries will be omitted from the resulting
	 * object.
	 *
	 * @param   Http_Header  header to resolve against
	 * @param   boolean      only return matched headings
	 * @return  array
	 * @uses    [Restful_Controller::_match_accept()]
	 */
	public function resolve_accept(Http_Header $header = NULL, $strict = FALSE)
	{
		// Retrieve accept headers
		$accept['accept']           = $this->accept();
		$accept['accept-charset']   = $this->accept_charset();
		$accept['accept-language']  = $this->accept_language();

		$result = array();

		// Sort values by quality
		$header->sort_values_by_quality();

		foreach ($accept as $_type => $_values)
		{
			if ($_values AND $header->offsetExists($_type) AND ($accept_header = $header[$_type]))
			{
				if (($match = Valid::match_accept_headers($_values, $accept_header)))
				{
					$result[$_type] = $match;
					continue;
				}
			}

			if ( ! $strict)
			{
				$result[$_type] = $_values[0];
			}
		}
		return $result;
	}
}