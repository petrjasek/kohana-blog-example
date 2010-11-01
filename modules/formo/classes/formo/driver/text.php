<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Text extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'text';
	public static $tag = 'input';
	public static $options = array
	(
		'type'	=> 'text'
	);

	public static function pre_render($field)
	{
		$field->attr('value', htmlentities($field->_value));
	}

}