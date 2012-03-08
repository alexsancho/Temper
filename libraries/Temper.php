<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Temper (Template Parser) Library
 * based on http://ioreader.com/2007/05/08/using-a-stack-to-parse-html/
 *
 * $Id: Temper.php 10 2008-07-20 23:08:13Z alex.aperez $
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
class Temper_Core {

    protected $buffer = NULL;
    protected $tag_prefix;
    protected $tag_handlers = array();

    /**
     * factory
     *
     * @param string $template template name
     * @param string $buffer buffer data
     * @return object
     * @access public
     * 
     */
    public static function factory($template = FALSE, $buffer = NULL)
    {
        return new Temper($template, $buffer);
    }

    /**
     * __construct
     *
     * @param string $template template name
     * @param string $buffer buffer data
     * @return void
     * @access public
     *
     */
    public function __construct($template = FALSE, $buffer = NULL)
    {
        spl_autoload_register(array('Temper', 'auto_load'));
        
        foreach(explode(',', Kohana::config('temper.tags')) as $tag)
        {
            $this->add_tag($tag);
        }

        $this->tag_prefix = Kohana::config('temper.prefix');

        $this->buffer = ($template) ? template::find($template) : $buffer;
    }
    
    /**
     * __toString
     *
     * @return string buffer data
     * @access public
     *
     */
    public function __toString()
    {
        return (string) $this->buffer;
    }
    
    /**
     * add_tag
     *
     * @param string $tag_name tag name
     * @return void
     * @access public
     *
     */
    public function add_tag($tag_name)
    {
        $class_name = 'Tag_'.ucfirst($tag_name);
        
        if ( ! class_exists($class_name))
            throw new Kohana_Exception('temper.unknown_tag', $class_name);
        
        $this->tag_handlers[$tag_name] = $class_name;
    }
    
    /**
     * get_tag_handler
     *
     * @param string $tag_name tag name
     * @return string tag handler class name
     * @access public
     *
     */
    public function get_tag_handler($prefix, $tag_name)
    {
        $ret = 'Tag_Unknown';
        
        if (isset($this->tag_handlers[$tag_name]))
            $ret = $this->tag_handlers[$tag_name];
        
        return new $ret($prefix, $tag_name);
    }
    
    /**
     * parse
     *
     * @param bool $write set to true to write output as view file
     * @param string $file output file name
     * @return object
     * @access public
     *
     */
    public function parse($write = FALSE, $file = 'temper')
    {
        if ( (int) Kohana::config('temper.allow_php') == 0)
        {
            $this->buffer = preg_replace('/<\?(?=php|=|\s).*?\?>/ms', '<!-- REMOVED -->', $this->buffer);
        }

        $this->parse_callback()->parse_tags();
        
        if ( (int) Kohana::config('temper.remove_comments') > 0)
            $this->buffer = preg_replace('/(^\s*)?<!-- #(.*?)-->/ms', '', $this->buffer);

        if ($write)
        {
            $file = APPPATH.'views/'.$file.EXT;
            $this->buffer = "<?php defined('SYSPATH') or die('No direct script access.'); ?>\n\n".$this->buffer;
            template::write($file, $this->buffer);
        }

        return $this;
    }

    /**
     * auto_load
     *
     * @param string $class
     * @return bool
     * @access public
     *
     */
    public static function auto_load($class)
    {
		if (class_exists($class, FALSE))
	    	return TRUE;

		if (($type = strrpos($class, 'Tag_')) !== FALSE)
		{
	    	$type = 'libraries/tags';
	    	$file = substr($class, 4);
            
            if (($filepath = Kohana::find_file($type, $file)) === FALSE)
                return FALSE;

            require_once $filepath;
		}

        return class_exists($class, FALSE);
    }

    /**
     * parse_callback
     *
     * @return object
     * @access protected
     *
     */
    protected function parse_callback()
    {
        $this->buffer = preg_replace_callback('~{(\%|\/|\=)([^}]*)?}~', array($this, 'parse_vars'), $this->buffer);
        $this->buffer = preg_replace_callback('~{{.*?}}~', array($this, 'parse_funcs'), $this->buffer);
        return $this;
    }

    /**
     * parse_vars
     *
     * @param array $matches array containing variable matches
     * @return string parsed string
     * @access protected
     *
     */
    protected function parse_vars($matches)
    {
        if ($matches[1] == '/')
        {
	    	if ($matches[0] == '{/}')
            {
				$matches[2] = '/';
	    	}

	    	$matches[2] = preg_replace_callback("~(\\=)([^/]+)~", array($this, 'parse_vars'), url::site($matches[2]));
		}
        else
        {
            $matches[2] = trim($matches[2]);

            if (strpos($matches[2], '|') !== FALSE)
            {
                $temp = explode('|', $matches[2]);
                $matches[2] = $temp[0];
                $filter = $temp[1];
            }

            if (strpos($matches[2], '.') !== FALSE)
            {
                $var = explode('.', $matches[2], 2);
                $matches[2] = 'template::get_var(\''.$var[1].'\', $'.$var[0].')';
            }
            else
            {
                $matches[2] = '$'.$matches[2];
            }

            $matches[2] = (isset($filter) AND function_exists($filter)) ? ''.$filter.'('.$matches[2].')' : $matches[2];

            if($matches[1] == '=')
                $matches[2] = '<?='. $matches[2] .';?>';
        }

        return $matches[2];
    }

    /**
     * parse_funcs
     *
     * @param string $matches
     * @return string
     *
     */
    protected function parse_funcs($matches)
    {
        if (preg_match('/^\{\{([a-zA-Z_0-9]+)\(+([^*]+)\)\}\}$/', $matches[0], $helpers)) 
		{
	    	if (is_callable($helpers[1], true)) 
	    	{
				$function = '<?='.$helpers[1].'('.$helpers[2].');?>';
	    	}
        }
		elseif (preg_match('/^\{\{([a-zA-Z_0-9]+)::([a-zA-Z_0-9]+)\(+([^*]+)\)\}\}$/', $matches[0], $helpers)) 
		{
	    	if (method_exists($helpers[1], $helpers[2])) 
	    	{
	    		$function = '<?= call_user_func_array(array(\''.$helpers[1].'\', \''.$helpers[2].'\'), array('.$helpers[3].')); ?>';
	    	}
        }

		if ( (int) Kohana::config('temper.allow_helpers') > 0 AND isset($function)) 
		{
	    	$matches[0] = $function;
		}
		elseif (isset($function))
		{
	    	$matches[0] = '<!-- REMOVED -->';
        }
        
        return $matches[0];
    }

    /**
     * parse_tags
     *
     * @return void
     * @access protected
     *
     */
    protected function parse_tags()
    {
		$parts = preg_split("~<(/?)([".preg_quote($this->tag_prefix)."]+)\:([a-z0-9_]+)((?: [^>]*)?)>~i", $this->buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
		$stack = array();
		$stack[] = new Tag_Base;
		$i = -1;
	
        while(isset($parts[++$i]))
        {
	    	$parent = end($stack);
	    	$key = $i % 5;
	    
            if ($key == 0)
            {
				$parent->buffer($parts[$i]);
	    	}
	    
            if (isset($parts[$i+4]))
            {
				$closing = trim($parts[$i+1] == '/');
				$tag_name = trim($parts[$i+3]);
				$attribs = trim($parts[$i+4]);
				$non_closing = FALSE;
                
                if ($attribs != '' AND $attribs{strlen($attribs)-1} == '/')
                {
		    		$non_closing = TRUE;
		    		$attribs = trim(substr($attribs, 0, -1));
				}
		
                if ($closing)
                {
		    		$tag = array_pop($stack);
		    		$parent = end($stack);
		    
                    if ($tag_name == $tag->get_name())
                    {
						$parent->buffer($tag->parse_buffer());
		    		}
                    else
                    {
						$parent->buffer('<!-- BAD CLOSING TAG FOR ['. $this->tag_prefix .':'. $tag_name .'] -->');
		    		}
				}
                else
                {
		    		$tag = $this->get_tag_handler($this->tag_prefix, $tag_name);

                    if ($attribs != '')
						$this->parse_tag_attributes($tag, $attribs);
		    
                    if ($non_closing)
                    {
						$parent->buffer($tag->parse_buffer());
		    		}
                    else
                    {
						$stack[] = $tag;
		    		}
				}
	    	}
	    	$i += 4;
		}
	
        $temp_buffer = '';
	
        while($node = array_pop($stack))
        {
	    	if ($node instanceof Tag_Base)
            {
				$node->buffer($temp_buffer);
				$this->buffer = $node->parse_buffer();
	    	}
	    	elseif ($node instanceof Temper_Tag)
            {
				$temp_buffer .= $node->buffer();
	    	}
            else
            {
                throw new Kohana_Exception('temper.instanceof', get_class($node));
            }
		}
        
        return $this;
    }
    
    /**
     * parse_tag_attributes
     *
     * @param object $tag
     * @param string $attrs
     * @return void
     * @access protected
     *
     */
    private function parse_tag_attributes(Temper_Tag $tag, $attrs = '')
    {
        $attributes = array();
		preg_match_all('~(?P<attr>[a-z]+)="(?P<val>[^"]*)"~i', $attrs, $parts);
	
        foreach($parts['attr'] as $i => $attr)
        {
	    	$attributes[strtolower($attr)] = $parts['val'][$i];
		}
        
        if ( ! empty($attributes))
        {
            $tag->add_attributes($attributes);
        }
    }

} //End Temper Library