<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkboxes extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'checkboxes';
	public static $tag = 'div';
	public static $options = array
	(
		'_value'	=> array()
	);
	
	public static function convert($field, $class)
	{
		if ( ! is_array($field->_value))
		{
			$field->_value = array();
		}
		
		parent::convert($field, $class);
	}
		
	public static function pre_render($field)
	{
		if ( ! is_array($field->_value)) $field->_value = array();
		foreach ($field->_values as $value => $name)
		{
			$opts = array
			(
				'type'	=> 'checkbox',
				'name'	=> $field->attr('name').'[]',
				'_value'	=> $value,
			);
			
			$checkbox = Driver_Checkbox::add($opts, 'Driver_Checkbox');
			if (in_array($checkbox->_value, $field->_value))
			{
				$checkbox->attr('checked', 'checked');
			}
			
			$field->add($checkbox);
		}
	}
	
}