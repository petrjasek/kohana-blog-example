<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Select extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'select';
	public static $tag = 'select';
	public static $options = array();
	
	public static function pre_render($field)
	{
		$field->add('option');
		foreach ($field->_values as $value => $name)
		{
			$field->add('option', str_replace(' ', '_', strtolower($value)), array('_text' => $name, 'value' => $value));
		}

		$option = $field->get($field->_value);
		($option AND $option->attr('selected', 'selected'));
	}

}