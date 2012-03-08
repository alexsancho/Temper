<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Temper Controller
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
class Temper_Controller extends Controller_Core {

	/**
	 * Prevents the execution of controller in production environment
	 */
    const ALLOW_PRODUCTION = FALSE;

	/**
	 * Class constructor
	 *
	 * @access public
	 * @author Alex Sancho
	 */
    public function __construct()
    {
        parent::__construct();
        
        Event::add('system.post_controller', array($this, '_display'));
    }

	/**
	 * Demo method
	 *
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    public function demo()
    {
        $template = 'multiply';

        $profiler = new Profiler;

        $temper = new Temper($template);
        
        $this->template = new View('temper');

        $this->template->unparsed = htmlentities($temper);

        $temper->parse(TRUE, $template);

        $this->template->parsed = htmlentities($temper);

        $evaled = new view($template);
        
        $evaled->a = 'b';
        $evaled->array = array(1,2,3,4,5,6);
        $evaled->user = array
        (
            'name' => 'kRuStYrUsTy',
            'id' => 1,
            'info' => array
			(
                'real_name' => 'John Doe',
            ),
        );
        
        $this->template->evaled = $evaled;
    }

	/**
	 * Display method
	 *
	 * @access public
	 * @return void
	 * @author Alex Sancho
	 */
    public function _display()
    {
        $this->template->render(true);
    }

} // End Temper Controller