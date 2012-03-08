<?php defined('SYSPATH') or die('No direct script access.');

class Temper_Controller extends Controller_Core {

    const ALLOW_PRODUCTION = FALSE;
    
    public function __construct()
    {
        parent::__construct();
        
        Event::add('system.post_controller', array($this, '_display'));
    }

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

    public function _display()
    {
        $this->template->render(true);
    }

} //End Temper Controller