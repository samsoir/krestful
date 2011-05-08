# krestful

An enhanced [RESTful](http://en.wikipedia.org/wiki/Representational_State_Transfer) 
interface for Kohana. This module extends existing Kohana Core classes to 
provide a more rigid RESTful interface that validates the following HTTP 
headers;

 * `accept`
 * `accept-charset`
 * `accept-language`

_krestful_ validates the Request header and attempts to match the request accept 
declarations with the `Restful_Controller` accept options. Results are returned 
in order of match, sorted by [quality](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html).

In strict mode, if no matching type is found the appropriate `HTTP_Exception`
will be thrown. Otherwise the Kohana defaults values will be used.

# Usage

To use _krestful_, ensure the KRestful module is enabled within the Kohana 
application. Within the `bootstrap.php` module add the following:

     Kohana::$modules(array(
          'krestful'    => MODPATH.'krestful'
     ));

_Note: This module modifies the inheritance tree for a number of Kohana Core
classes. Ensure any inheritance conflicts are resolved within your application._

Once the _krestful_ module is loaded, controllers can extend
`Controller_REST` safely and receive the enhancements of this module.

The following properties can be set to the `Restful_Controller` class.

 1. `Restful_Controller::$_accept`
    Defines the acceptable types (mime) the controller supports.
    _e.g._ `text/html`, `application/json`, `application/xml`, ...
 2. `Restful_Controller::$_accept_charset`
    Defines the acceptable character sets the controller supports.
    _e.g._ `utf-8`, 'utf-16', 'ISO-8859-1', ...
 3. `Restful_Controller::$_accept_lang`
    Defines the acceptable languages the controller supports.
    _e.g._ `en_US`, `en_GB`, `fr_FR`
 4. `Restful_Controller::$_accept_strict`
    Defines if the controller will be strict and enforce on the defined
    acceptable arguments upon request.

The acceptable request parameters should be set in the controller definition.
For example:

    class Controller_Foo extends Controller_REST {
    
         protected $_accept = array(
              'text/html',
              'application/json'
              'application/xml'
         );
    
         protected $_accept_lang = array(
              'en_US',
              'en_GB'
         );
    
         protected $_accept_charset = array(
              'utf-8',
              'ISO-8859-1'
         );
    
         protected $_accept_strict = TRUE;
    }

The following controller will only return either;

 * _HTML_, _JSON_ or _XML_ in
 * _US_ or _British_ English using
 * _UTF-8_ or _ISO-8859-1_ encoding

# System Requirements

 * [Kohana Framework](http://kohanaframework.org) >= 3.1.0
 * [PHP](http://www.php.net) >= 5.3.0

# License

This software is protected by the [ISC License](http://www.opensource.org/licenses/isc-license).

Copyright (c) 2011, Sam de Freyssinet

Permission to use, copy, modify, and/or distribute this software for any purpose 
with or without fee is hereby granted, provided that the above copyright notice 
and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH 
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND 
FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, 
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS 
OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER 
TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF 
THIS SOFTWARE.