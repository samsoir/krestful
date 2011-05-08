<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Extended HTTP_Header providing encapsulated resolution of the HTTP Accept:
 * headers for `accept`; `accept-charset`; and `accept-language`.
 *
 * @package   Restful
 * @category  HTTP_Header
 * @author    Sam de Freyssinet
 */
class Restful_Header extends Kohana_Http_Header {

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
	 * @param   Restful_Controller  controller resolve against
	 * @param   boolean      only return matched headings
	 * @return  array
	 * @throws  HTTP_Exception
	 * @uses    [Restful_Controller::_match_accept()]
	 */
	public function resolve_accept(Restful_Controller $controller, $strict = FALSE)
	{
		// Retrieve accept headers
		$accept['accept']           = $controller->accept();
		$accept['accept-charset']   = $controller->accept_charset();
		$accept['accept-language']  = $controller->accept_language();

		$result = array();

		// Sort values by quality
		$this->sort_values_by_quality();

		// Match against accept criteria
		array_walk($accept, function ($type, $values) use ( & $result, $this)
			{
				if ($values AND $this->offsetExists($type)
				            AND ($accept_header = $this[$type]))
				{
					if ($match = Valid::match_accept_headers($values, $accept_header))
					{
						$result[$type] = $match;
						return;
					}
				}

				if ( ! $strict)
				{
					$result[$type] = $values[0];
				}
			});

		return $result;
	}

} // End Restful_Header