<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Password extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'password';
	public static $tag = 'input';
	public static $options = array
	(
		'type'	=> 'password',
	);

	public static function pre_render($field)
	{
		$field->attr('value', $field->_value);
	}

}