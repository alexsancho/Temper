<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Temper Helper
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

class template_Core {

	/**
	 * Find template file
	 *
 	 * @param string $file name of template
 	 * @param mixed $ext should be a custom extension name or true to use default
	 * @access public
	 * @return mixed template content or false if fail
	 * @static
	 * @author Alex Sancho
	 */
	public static function find($file, $ext = true)
	{
		$ext = ($ext != true) ? $ext : Kohana::config('temper.extension');
		
		if ($src = Kohana::find_file('templates', $file, FALSE, $ext))
		{
			return file_get_contents($src);
		}
		
		return false;
	}
	
	/**
	 * get_var
	 *
	 * @param string $key
	 * @param mixed $obj
	 * @access public
	 * @return mixed
	 * @static
	 * @author Alex Sancho
	 */
	public static function get_var($key, $obj)
	{
		if (is_object($obj) and is_string($key))
		{
			$key = str_replace('.', '->', $key);

			if (method_exists($obj, $key))
			{
				return call_user_func(array($obj, $key));
			}

			return $obj->{$key};
		}

		return Kohana::key_string($obj, $key);
	}

	/**
	 * get_list
	 *
	 * @param string
	 * @access public
	 * @return array
	 * @author Alex Sancho
	 */
	public function get_list($path = 'templates')
	{
		$ext = ltrim(Kohana::config('temper.extension'), '.');
		$path = APPPATH.'/'.$path.'/';
		$templates = array(0 => 'Select template');

		if (($dir = @dir($path)) !== false)
		{
			while(($file = $dir->read()) !== false)
			{
				if (is_file($path . $file) and preg_match('/\.'.$ext.'$/i', $file))
				{
					list($temp, $ext) = explode('.', $file);
					$templates[$temp] = $temp;
				}
			}
		}

		return $templates;
	}

	/**
	 * write
	 *
	 * @param string $file file name with full path
	 * @param string $buffer data to write
	 * @access public
	 * @return void
	 * @throws Kohana_Exception
	 * @static
	 * @author Alex Sancho
	 */
	public static function write($file, $buffer)
	{
		if ( ! $fp = fopen($file, 'w'))
			throw new Kohana_Exception('temper.write_error');

		if (fwrite($fp, $buffer) === FALSE)
			throw new Kohana_Exception('temper.write_permissions');

		fclose($fp);

		@chmod($file, 0666);
	}

} // End Template Helper