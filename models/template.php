<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Template Model.
 *
 * $Id: template.php 12 2008-09-11 08:42:02Z alex.aperez $
 *
 * @package    	Temper Module
 * @author     	Alex Sancho
 * @copyright	(c) 2008 Alex Sancho
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
 */
class Template_Model extends Model 
{
    /**
	 * save
	 *
	 * @param object $post
	 * @param string $path
	 * @return bool
	 * @access public
	 *
	 */
	public function save(Validation $post, $path = 'pages/')
	{
		if ($post->submitted() and $data = $post->as_array())
		{
			Temper::factory(FALSE, $data['filecontent'])->parse(TRUE, $path.$data['filename']);
			
			$ext = ($ext = Kohana::config('temper.extension')) ? ltrim($ext, '.') : 'tpl';
			
			template::write(APPPATH.'templates/'.$data['filename'].'.'.$ext, $data['filecontent']);
		}
		
		return true;
	}

	/**
	 * delete
	 * 
	 * @param string $id
	 * @return boolean
	 * @access public
	 * 
	 */
	public function delete($id)
	{
		$ext = ($ext = Kohana::config('temper.extension')) ? ltrim($ext, '.') : 'tpl';
	
		return unlink(APPPATH.'templates/'.$id.'.'.$ext);
	}

} //End Template Model