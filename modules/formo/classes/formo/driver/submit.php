<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Submit extends Fdriver {

	public static $view = 'formo/submit';
	public static $driver = 'submit';
	public static $tag = 'input';
	public static $options = array
	(
		'type'	=> 'submit'
	);

	public static function pre_render($field)
	{
		$field->attr('value', htmlentities($field->_value));
	}

}