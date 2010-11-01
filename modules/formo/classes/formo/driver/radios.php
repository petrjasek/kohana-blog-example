<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radios extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'radios';
	public static $tag = 'div';
	public static $options = array();
	
	public static function pre_render($field)
	{
		foreach ($field->_values as $value => $name)
		{
			$opts = array
			(
				'type'	=> 'radio',
				'name'	=> $field->attr('name'),
				'_value'	=> $value,
			);
			
			$radio = Driver_Radio::add($opts, 'Driver_Radio');
			
			if ($radio->_value == $field->_value)
			{
				$radio->attr('checked', 'checked');
			}
			
			$field->add($radio);
		}

	}

}