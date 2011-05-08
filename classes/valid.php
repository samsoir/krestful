<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Extensions for the Kohana Valid class to support
 * matching of types
 *
 * @package  Restful
 * @category Valid
 * @author Sam de Freyssinet
 */
class Valid extends Kohana_Valid {

	/**
	 * Matches the correct accept header to the acceptable
	 * values supplied in the request header.
	 *
	 * @param   array    acceptable_values 
	 * @param   array|string accept_header_components
	 * @return  string
	 * @return  boolean
	 */
	public static function match_accept_headers(array $acceptable_values, $accept_header_components)
	{
		if ( ! is_array($accept_header_components))
		{
			$accept_header_components = array($accept_header_components);
		}

		foreach ($accept_header_components as $component)
		{
			if ( ! in_array(($value = strtolower((string) $component->value())), $acceptable_values))
				continue;

			return $value;
		}

		return FALSE;
	}
} // End Valid