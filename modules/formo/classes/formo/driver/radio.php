<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radio extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'radio';
	public static $tag = 'input';
	public static $options = array
	(
		'type'	=> 'radio'
	);

}