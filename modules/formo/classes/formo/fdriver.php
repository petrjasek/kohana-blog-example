<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Fdriver {

	public static $view = 'formo/default';
	public static $driver = 'text';
	public static $tag = 'div';
	public static $options = array();
	
	// How to handle adding
	public static function add($options, $driver)
	{
		// This is simply because late static binding doesn't exist PHP pre-5.3
		$class = new ReflectionClass($driver);
		$options = array_merge($options, $class->getStaticPropertyValue('options'));
		
		// Make the id if it wasn't set
		if (empty($options['id']))
		{
			$options['id'] = $options['name'];
		}
		
		return FField::factory($class->getStaticPropertyValue('tag'), $options);
	}
	
	// How to add post values
	public static function add_post($field)
	{
		return;
	}
		
	// Convert to a this driver
	public static function convert($field, $driver)
	{
		$class = new ReflectionClass($driver);
		
		$field->_driver = $class->getStaticPropertyValue('driver');
		$field->_tag = $class->getStaticPropertyValue('tag');
		
		$field->option($class->getStaticPropertyValue('options'));
	}	
		
	public static function pre_render($field)
	{
		$field->text($field->_value);
	}
}