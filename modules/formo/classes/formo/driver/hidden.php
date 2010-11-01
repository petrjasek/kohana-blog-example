<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Hidden extends Fdriver {
	public static $view = 'formo/hidden';
	public static $driver = 'hidden';
	public static $tag = 'input';
	public static $options = array
	(
		'type'	=> 'hidden'
	);

	public static function pre_render($field)
	{
		$field->attr('value', htmlentities($field->_value));
	}	
}