<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Temper Tag
 *
 * LICENSE
 * 
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of copyright holders nor the names of its
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @version    $Id
 * @package    Temper
 * @author     Alex Sancho <alex@alexsancho.name>
 * @copyright  (c) 2008 Alex Sancho
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
abstract class Temper_Tag implements ArrayAccess {

	/**
	 * Buffer
	 *
	 * @var string
	 * @access protected
	 */
    protected $buffer = '';

	/**
	 * Prefix
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = '';

	/**
	 * Name
	 *
	 * @var string
	 * @access protected
	 */
	protected $name = '';

	/**
	 * Attributes
	 *
	 * @var array
	 * @access private
	 */
    private $attributes = array();

	/**
	 * Class Constructor
	 *
	 * @param string $prefix 
	 * @param string $name 
	 * @access public
	 * @author Alex Sancho
	 */
    public function __construct($prefix = '', $name = '')
    {
        $this->prefix = $prefix;
		$this->name = $name;
    }

	/**
	 * Require Attributes
	 *
	 * @access public
	 * @return void
	 * @throws Kohana_Exception
	 * @author Alex Sancho
	 */
    public function require_attributes()
    {
		foreach(func_get_args() as $attrib)
        {
	    	if ( ! array_key_exists($attrib, $this->attributes))
            {
                throw new Kohana_Exception('temper.missing_attrib', $attrib, $this->name);
	    	}
		}
    }


	/**
	 * Require One
	 *
	 * @access public
	 * @return string
	 * @throws Kohana_Exception
	 * @author Alex Sancho
	 */
    public function require_one()
    {
		$ret = FALSE;
        $attribs = func_get_args();

        foreach($attribs as $attrib)
        {
	    	if (array_key_exists($attrib, $this->attributes))
            {
				$ret = $attrib;
				break;
	    	}
		}

		if ($ret === FALSE)
        {
            throw new Kohana_Exception('temper.missing_attribs',implode(',', $attribs), $this->name);
		}

		return $ret;
    }

	/**
	 * Buffer
	 *
	 * @param mixed $buffer 
	 * @access public
	 * @return mixed
	 * @author Alex Sancho
	 */
    public function buffer($buffer = FALSE)
    {
		if ($buffer !== FALSE)
        {
	    	$this->buffer .= $buffer;
		}
        else
        {
	    	return $this->buffer;
		}
    }

	/**
	 * Add Attributes
	 *
	 * @param array $array 
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    public function add_attributes(array $array = array())
    {
		$this->attributes = array_merge($this->attributes, $array);
    }

	/**
	 * Get Name
	 *
	 * @access public
	 * @return string
	 * @author Alex Sancho
	 */
    public function get_name()
    {
		return $this->name;
    }

	/**
	 * Offset Get
	 *
	 * @param int $key 
	 * @access public
	 * @return mixed
	 * @author Alex Sancho
	 */
    public function offsetGet($key)
    {
		return $this->offsetExists($key) ? $this->attributes[$key] : NULL;
    }

	/**
	 * Offset Set
	 *
	 * @param int $key 
	 * @param mixed $val 
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    public function offsetSet($key, $val)
    {
		$this->attributes[$key] = $val;
    }

	/**
	 * Offset Exist
	 *
	 * @param int $key 
	 * @access public
	 * @return bool
	 * @author Alex Sancho
	 */
    public function offsetExists($key)
    {
		return isset($this->attributes[$key]);
    }

	/**
	 * Offset Unset
	 *
	 * @param int $key 
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    public function offsetUnset($key)
    {
		unset($this->attributes[$key]);
    }

	/**
	 * Parse Buffer
	 *
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    abstract public function parse_buffer();

} // End Temper Tag