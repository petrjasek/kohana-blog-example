<?php defined('SYSPATH') or die('No direct script access.');

class Formo_FField extends DOM {

	// The current field's driver
	public $_driver = 'text';

	// The current field's value
	public $_value = NULL;

	// Used for select, checkboxes, etc where key => value are needed
	public $_values = array();
	
	// The field's error message
	public $_error = FALSE;
	
	// Return new Field	
	public static function factory($tag = 'input', $str = NULL, array $options = NULL)
	{
		return new FField($tag, $str, $options);
	}
	
	// Create a new Field
	public function __construct($tag = 'input', $str = NULL, array $options = NULL)
	{
		$key = array_search('value', $this->_attributes);
		unset($this->_attributes[$key]);
				
		parent::__construct($tag, $str, $options);
		
		$this->add_event('pre_render', array($this, 'driver'), array('pre_render'));
	}

	public function __call($func, $args)
	{
		// If the field exists
		if ($field = $this->get($func))
		{
			if (isset($args[0]))
			{
				$field->value = $args[0];
				return $this;
			}
			else
			{
				return $field->value;
			}
		}
		
		$this->$func = $args[0];
		
		return $this;
	}
	
	// Return the class name based on the driver
	public static function driver_class($driver)
	{
		return 'Driver_'.ucfirst($driver);
	}
	
	// Run a method in the field's driver	
	public function driver($method, array $data = NULL)
	{
		// Always make this field the first param passed
		$data = ($data) ? array_merge(array($this), $data) : array($this);
		
		$class = new ReflectionMethod(self::driver_class($this->_driver), $method);
		
		$class->invokeArgs(NULL, $data);
		
		return $this;
	}
	
	// Convert this field to another
	public function convert($to)
	{
		$class = $this->driver_class($to);
		call_user_func(array($class, 'convert'), $this, $class);
		
		return $this;
	}
		
	// Allow chaining while setting variables
	public function set($param, $value)
	{
		$this->$param = $value;
	}
	
	// Set this field's error message	
	public function error($message, array $params = NULL)
	{
		$this->parent()->_validate->error($this->attr('name'), $message, $params);
		$this->_error = $message;		
		
		return $this;
	}
	
	// Run validation on this field
	public function validate($return_error = FALSE)
	{	
		$val_obj = Validation::factory(array($this->name => $this->_value))
			->add_rules($this->name, 'required', 'valid::email');
		
		$errors = $this->parent()->validate($val_obj);
		
		(isset($errors[$this->name]) AND $this->error = $errors[$this->name]);
		
		return $return_error ? $this->error : ($this->error === FALSE);
	}
	
	// Render this field
	public function render($view = FALSE)
	{
		( ! $this->has_run('pre_render') AND $this->run_event('pre_render'));
		
		if ($view === TRUE)
		{
			$class = new ReflectionClass($this->driver_class($this->_driver));
			$view = $class->getStaticPropertyValue('view');
		}

		if ($view)
			return View::factory($view, $this->data())
				->bind('field', $this);
				
		return parent::render();
	}

}