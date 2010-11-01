<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkbox extends Fdriver {

	public static $view = 'formo/text';
	public static $driver = 'checkboxes';
	public static $tag = 'input';
	public static $options = array
	(
		'type' => 'checkbox',
	);

}