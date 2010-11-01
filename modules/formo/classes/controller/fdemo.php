<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Fdemo extends Controller_Template {

	public $template = 'f_template';

	protected $title = 'Formo 2.0b Preview';
	protected $content;
	protected $scripts;
	protected $header;
	protected $footer;


	public function before()
	{
		parent::before();
				
		$this->template
			->bind('title', $this->title)
			->bind('scripts', $this->scripts)
			->bind('header', $this->header)
			->bind('content', $this->content);		
	}

	// This is a very, very simple rundown of a few things -- Y'know, like a playground
	public function action_index()
	{
		$form = Formo::factory()
			->attr('class', 'standardform')
			->add('username', array('rules' => array('not_empty' => NULL)))
			->add('email')
			->rule('email', 'not_empty')
			->rule('email', 'email')
			->add_group('address')
			->add('submit', 'submit');			
								
		$this->content = View::factory('formo/form')
			->bind('form', $form);

		if ($form->values($_POST)->validate())
		{
			$this->content .= '<h1>VALIDATED!!!!!</h1>';
		}
	}
		
}