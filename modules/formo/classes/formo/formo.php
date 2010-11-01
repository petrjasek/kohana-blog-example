<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Formo extends DOM {

	// Allows multiple formo forms on a page
	protected $_formo_name = 'default';
	
	// Quick-loading default stuff
	protected $_formo_type;
	
	// Flag whether form was sent
	protected $_sent = FALSE;
	
	// Config stuff
	public $_config;
	public $_groups;
	
	public $_order = array();
	
	// Cached set of elements
	protected $_els;
	
	// The validate object for this form	
	public $_validate;
	
	// Type of data
	public $_type = 'post';
	
	// Form plugins	
	protected static $_plugins = array();

	// Return new Form DOM element					
	public static function factory($name = NULL, $type = NULL, array $options = NULL)
	{
		return new Formo($name, $type, $options);
	}
	
	// Build form object from options
	public function __construct($name = NULL, $type = NULL, array $options = NULL)
	{
		$options = func_get_args();
		self::args(__CLASS__, '__construct', $options);
		
		$options['method'] = ( ! empty($options['method'])) ? $options['method'] : 'post';
		$options['action'] = ( ! empty($options['action'])) ? $options['action'] : '';
		
		$config = Kohana::config('formo');
		$this->_groups = (isset($config->groups)) ? $config->groups : array();

		$this->_config = $config->default;
		if ( ! empty($options['_formo_type']) AND isset($this->_config->types[$options['_formo_type']]))
		{
			$config = $config->types[$options['_formo_type']];
		}
		
		$this->_validate = new Validate(array());
		
		$this->add('hidden', '_formo', array('_value' => $this->_formo_name));
		$this->sent();
									
		return parent::__construct('form', $options);
	}
	
	public function __call($func, $args)
	{
		// Call validate functions
		if (method_exists($this->_validate, $func))
		{
			call_user_func_array(array($this->_validate, $func), $args);
			return $this;
		}

		// If the field exists
		if ($field = $this->get($func))
		{
			// If an arg was passed, set the field's value
			if (isset($args[0]))
			{
				$field->value = $args[0];
				return $this;
			}
			// If not, return its value
			else
			{
				return $field->value;
			}
		}
		
		return $this;
	}
	
	// Add a field to the Form object
	public function add($_driver, $name = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = self::args(__CLASS__, 'add', $options);
				
		// If a driver is named but not a name, make the driver text and the name the driver
		if (empty($options['name']))
		{
			$options['name'] = $options['_driver'];
			if ( ! isset($orig_options['_driver']))
			{
				$options['_driver'] = 'text';
			}
		}
				
		// Merge with defaults
		if ($defaults = $this->config('defaults') AND isset($defaults[$options['name']]))
		{
			$options = array_merge($options, $defaults[$options['name']]);
		}

		$class = Ffield::driver_class($options['_driver']);
		
		// Allow loading rules, callbacks, filters upon adding a field
		$validate_settings = array('rules', 'callbacks', 'filters');
		foreach ($validate_settings as $setting)
		{
			if ( ! empty($options[$setting]))
			{
				$this->_validate->$setting($options['name'], $options[$setting]);
				unset($options[$setting]);
			}
		}
				
		// Use the appropriate driver to create the field
		$field = call_user_func(array(Ffield::driver_class($options['_driver']), 'add'), $options, $class);

		
		// Add the new field
		parent::add($field, $options);
		// Make the new field an "expected" field in the validate object
		$this->_validate->label($options['name'], $options['name']);
		
		return $this;
	}
	
	// Add a group of fields based on passed array or config file group
	public function add_group($group)
	{
		if (is_array($group))
		{
			foreach ($group as $_group)
			{
				if ( ! is_array($_group))
				{
					$this->add_group($group);
				}
				else
				{
					foreach ($_group as $name => $settings)
					{
						$this->_groups[$name] = $settings;
						$this->add_group($name);
					}
				}
			}
			
			return $this;
		}
		
		if ( ! isset($this->_groups[$group]))
		{
			throw new Kohana_Exception('You must define a group before adding it to the form');
		}
		else
		{
			foreach ($this->_groups[$group] as $name => $options)
			{
				$this->add($name, $options);
			}
		}
		
		return $this;
	}
	
	// Set a variable inside a field to something with chaining
	public function set($field, $param)
	{
		$args = func_get_args();
		if ( ! isset($args[2]))
		{
			$form->$field = $param;
		}
		else
		{
			$form->get($field)->set($param, $args[2]);
		}
		
		return $this;
	}
		
	// Grab the elements
	public function fields($force_new = FALSE)
	{
		// Grab cache because we use 'em all the time
		if ($force_new === FALSE AND $this->_els)
			return $this->_els;
			
		$unordered = array();
		$ordered = array();
		
		foreach ($this->_elements as $vals)
		{
			list($name, $element) = $vals;
						
			if (in_array($element, $this->_order))
			{
				$key = array_search($element, $_order);
				$ordered[$key] = $element;
			}
			else
			{
				$unordered[] = $element;
			}
		}
		
		$this->_els = array_merge($ordered, $unordered);
		
		return $this->_els;
	}
	
	// Determine whether the form was sent
	public function sent()
	{
		$input = NULL;
		switch (strtolower($this->_type))
		{
			case 'get':
				$input = $_GET;
				break;
			case 'post':
			default:
				$input = $_POST;
		}
		
		if ($input !== NULL AND ! empty($input['_formo']) AND $input['_formo'] == $this->_formo_name)
		{
			$this->_sent = TRUE;
		}			
			
		return $this->_sent;
	}
	
	// Load data, auto-works with get/post
	public function values(array $input = NULL)
	{
		if ($input === NULL)
		{
			switch (strtolower($this->_type))
			{
				case 'get':
					$input = $_GET;
				break;
				default:
					$input = $_POST;
			}
		}
		
		foreach ($this->fields() as $field)
		{
			if (isset($input[$field->attr('name')]))
			{
				$field->_value = $input[$field->attr('name')];
				
				// Let the driver determine how a data should be updated
				$field->driver('add_post');
			}
		}
		
		return $this;
	}
	
	// Return this form's config items	
	public function config($item, $default = FALSE)
	{
		return (isset($this->_config[$item])) ? $this->_config[$item] : $default;
	}
	
	// Validate, allows forcing validation on unsent data and overriding validation object
	public function validate($validate_if_not_sent = FALSE, $options = FALSE)
	{
		if ($validate_if_not_sent === FALSE AND $this->_sent === FALSE)
			return FALSE;
		
		// Replace the validation object
		if ($options instanceof Validate)
		{
			$this->_validate = $validate;
		}
		// Exchange values for validating
		else
		{
			$values = (is_array($options)) ? $options : $this->as_array('_value');
			$this->_validate->exchangeArray($values);
		}
								
		$check = $this->_validate->check();
		
		// Attach errors to each field
		foreach ($this->errors() as $field => $error)
		{
			$this->get($field)->_error = $error;
		}
		
		return $check;
	}
	
	// Call ORM drivers specifically. This requires ORM settings in the config file
	public function orm($method, & $model)
	{
		
		return $this;
	}
	
	// Plugins function on the whole form
	public function plugin($plugin, $method, array $data = NULL)
	{
		// Merge the field with the rest of the data
		$data = ($data) ? array_merge(array($this), $data) : array($this);
		
		call_user_func_array(array($plugin, $method), $data);
		
		return $this;
	}
	
	// Run a driver function on a field
	public function driver($method, $field, array $data = NULL)
	{
		// If the field is an object, use that object
		$field = ($field instanceof Ffield === FALSE) ? $this->get($field) : $field;
		
		$data = ($data === NULL) ? array() : $data;

		$field->driver($method, $data);
				
		return $this;
	}
	
	// Return Validate object's error messages
	public function errors($file = 'validate', $translate = TRUE)
	{		
		return $this->_validate->errors($file, $translate);			
	}
	
	// Set a specific field's errors
	public function error($field, $message, array $params = NULL)
	{
		$this->get($field)->error($message, $params);
				
		return $this;
	}
	
	// Return rendered from and its fields
	public function render($view = FALSE)
	{
		$args = func_get_args();
		if ( ! isset($args[0]))
		{
			$view = $this->config('render_as_views');
		}
				
		($this->config('auto_post') AND $this->add_posts());
		
		if ($view AND $view !== TRUE)
		{
			return View::factory($view)
				->bind('form', $this);
		}
		
		return parent::render($view);
	}
			
}