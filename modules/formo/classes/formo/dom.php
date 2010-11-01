<?php defined('SYSPATH') or die('No direct script access.');

class Formo_DOM {

	// Possible html attributes
	protected $_attributes = array
	(
		// regular elements
		'id', 'class', 'name', 'rel', 'autofocus', 'href', 'onclick', 'onfocus', 'obsubmit',
		// Form elements
		'type', 'value', 'rows', 'columns', 'size', 'max_size', 'method', 'action', 'onfocus',
		'onsubmit', 'selected', 'checked', 'disabled',
	);
	
	// End with /> on the first tag
	protected $_singles = array
	(
		'img', 'input', 'br'
	);
	
	// Store arbitrary information for object
	protected $_data = array();
	
	// This tag
	protected $_tag = 'div';
	
	// Element text
	protected $_text;
	
	// Styles for this object
	protected $_styles = array();
	
	// Attributes for this object
	protected $_attr = array();
	
	// Elements inside this object
	protected $_elements = array();
	
	// The element this resides in
	public $_parent = FALSE;
	
	// DOM events
	protected $_events = array();
	// Events that have run
	protected $_events_run = array();
	
	// Quote mark to use
	protected $_quote = '"';
	
	// Returns new DOM object
	public static function factory($_tag = 'div', $_text = NULL, array $options = NULL)
	{
		return new DOM($_tag, $_text, $options);
	}
	
	public function __construct($_tag = 'div', $_text = NULL, array $options = NULL)
	{
		// Build options array
		$options = func_get_args();
		self::args(__CLASS__, '__construct', $options);
		
		$this->options($options);
	}
	
	public function __set($attr, $value)
	{
		// Add DOM objects to _elements array
		if ($value instanceof DOM)
		{
			$this->_elements[] = array($attr, $value);
		}
		else
		{
			$this->$attr = $value;
		}
	}
	
	// Fetch a DOM element by name
	public function __get($name)
	{
		return($this->get($name));
	}
	
	// Render when echoed
	public function __toString()
	{
		return $this->render();
	}
	
	// Simplifies taking function arguments
	// Turn all arguments into one nice $options array
	public static function args($class, $method, array & $args)
	{
		$method = new ReflectionMethod($class, $method);
		
		$options = array();
		$original_options = array();
				
		$i = 0;
		foreach ($method->getParameters() as $param)
		{
			if ( ! isset($args[$i]) AND ! isset($defaults[$param->name]))
				continue;
				
			if ( ! isset($args[$i]))
			{
				$options[$param->name] = $defaults[$param->name];
			}
				
			if (is_array($args[$i]))
			{
				foreach ($args[$i] as $key => $value)
				{
					$options[$key] = $value;
					$original_options[$key] = $value;
				}
			}
			else
			{
				$options[$param->name] = $args[$i];
			}
			
			$i++;
		}
		
		$args = $options;
		
		return $original_options;
	}
		
	// Interpret and set options passed into construct
	public function options(array $options)
	{
		foreach ($options as $option => $value)
		{
			if (in_array($option, $this->_attributes))
			{
				$this->attr($option, $value);
			}
			elseif ($option == 'style')
			{
				$styles = explode(';', $value);
				foreach ($styles as $style)
				{
					$values = explode(':', $style);
					$this->css(trim($values[0]), trim($values[1]));
				}
			}
			else
			{
				$this->$option = $value;
			}
		}
		
		return $this;
	}
	
	// Add a DOM element to this element
	public function add($tag, $name = NULL, array $options = NULL)
	{
		$place_where = FALSE;
		$place_around = NULL;
		
		if ( ! is_object($tag))
		{
			if (is_array($tag))
			{
				$options = $tag;
			}
			elseif (is_array($name))
			{
				$options = $name;
				$name = (isset($options['name'])) ? $options['name'] : NULL;
			}
			
			if (isset($options['before']))
			{
				$place_where = 'before';
				$place_around = $options['before'];
				unset($options['before']);
			}
			
			if (isset($options['after']))
			{
				$place_where = 'after';
				$place_around = $options['after'];
				unset($options['after']);
			}
			
			if (is_array($tag))
			{
				$tag = ( ! empty($options['tag'])) ? $options['tag'] : self::$tag;
			}
			
			$options['_parent'] = $this;
			
			$element = DOM::factory($tag, $options);
		}
		else
		{
			$element = $tag;
			$element->_parent = $this;
			$name = $element->attr('name');
		}
		
		$name = $name ? $name : count($this->_elements);
				
		if ($place_where === FALSE)
		{
			$this->$name = $element;
		}
		else
		{
			$place_pos = (is_int($place_around)) ? $place_around : NULL;
			
			if ( ! $place_pos)
			{

			}
		}
		
		return $this;
	}
	
	// Add class to element
	public function add_class($class)
	{
		if (is_array($class))
		{
			foreach ($class as $_class)
			{
				$this->add_class($_class);
			}
			
			return $this;
		}
		
		$this->_attr['class'] = ( ! empty($this->_attr['class']))
			? $this->_attr['class'].' '.$class
			: $class;
			
		return $this;
	}
	
	// Remove a class if it exists
	public function remove_class($class)
	{
		(preg_match('/ /', $class) AND $class = explode(' ', $class));
		
		if (is_array($class))
		{
			foreach ($class as $_class)
			{
				$this->remove_class(trim($_class));
			}
			
			return $this;
		}
				
		$search = array
		(
			'/^'.$class.' /',
			'/ '.$class.'( )/',
			'/ '.$class.'$/'
		);
		
		$this->_attr['class'] = preg_replace($search,'$1',$this->_attr['class']);
		
		if ( ! $this->_attr['class'])
		{
			unset($this->_attr['class']);
		}
		else
		{
			$this->_attr['class'] = trim($this->_attr['class']);
		}
		
		return $this;
	}
	
	// Remove a DOM element by its numerical offset
	public function remove($offset)
	{
		if (is_array($offset))
		{
			foreach ($offset as $_offset)
			{
				$this->remove($_offset);
			}
			
			return $this;
		}
				
		unset($this->_elements[$offset]);
		
		return $this;
	}
	
	// Replace a DOM element by its numerical offset
	public function replace($offset, $element)
	{
		if (is_array($offset))
		{
			foreach($offset as $_offset => $_element)
			{
				$this->replace($_offset, $_element);
			}
			
			return $this;
		}
		// If an array of elements is passed fill the array with 'em
		if (is_array($element))
		{
			$elements = array();
			foreach ($this->_elements as $_offset => $vals)
			{
				if ($_offset == $offset)
				{
					foreach ($element as $_element)
					{
						$elements[] = array($_element->attr('name'), $_element);
					}
					
					continue;
				}
				
				$elements[] = $vals;
			}
									
			$this->_elements = $elements;
			
			return $this;
		}
		
		$this->_elements[$offset] = array($element->attr('name'), $element);
		
		return $this;
	}
	
	// Set or return $this->_data items
	public function data()
	{
		$vals = func_get_args();

		if ( ! $vals)
			return $this->_data;
		
		$name = $vals[0];
		$vals = array_slice($vals, 1);
		
		if ( ! isset($vals[0]))
			return (isset($this->_data[$name])) ? $this->_data[$name] : NULL;
		
		if (is_array($name))
		{
			foreach ($name as $_name => $value)
			{
				$this->data($_name, $value);
			}
		}
		else
		{
			$this->_data[$name] = $vals[0];
		}
		
		return $this;
	}
	
	// Add styles to a DOM element
	public function css($style)
	{
		$vals = array_slice(func_get_args(), 1);
				
		// Return the value if requested
		if ( ! is_array($style) AND ! isset($vals[0]))
			return (isset($this->_styles[$style])) ? $this->_styles[$style] : NULL;
		
		if (is_array($style))
		{
			foreach ($style as $_style => $val)
			{
				$this->css($_style, $val);
			}
		}
		elseif ( ! $vals[0])
		{
			unset($this->_styles[$style]);
		}
		else
		{
			$this->_styles[$style] = $vals[0];
		}
				
		return $this;
	}
	
	// Return this element's parent
	public function parent()
	{
		return $this->_parent;
	}
				
	// Set an html attribute
	public function attr($name)
	{
		$vals = array_slice(func_get_args(), 1);
				
		// Return the value if that's all that was requested
		if ( ! is_array($name) AND ! isset($vals[0]))
			return (isset($this->_attr[$name])) ? $this->_attr[$name] : NULL;
		
		if (is_array($name))
		{
			foreach ($name as $_name => $value)
			{
				$this->attr($_name, $value);
			}
		}
		elseif ( ! $vals[0])
		{
			unset($this->_attr[$name]);
		}
		else
		{
			$this->_attr[$name] = $vals[0];
		}
		
		return $this;
	}
		
	// Set or return the text
	public function text()
	{
		$vals = func_get_args();
		
		// Return the text if nothing was entered
		if ( ! $vals)
			return $this->_text;
			
		$this->_text = $vals[0];
		
		return $this;
	}
	
	// Built-in DOM element events, based on KO2 events but instance-based
	public function add_event($name, $callback, array $args = NULL)
	{
		if ( ! isset($this->_events[$name]))
		{
			// Create an empty event if it is not yet defined
			$this->_events[$name] = array();
		}
		elseif (in_array($callback, $this->_events[$name], TRUE))
		{
			// The event already exists
			return FALSE;
		}

		// Add the event
		$this->_events[$name][] = array($callback, $args);

		return TRUE;
	}

	// Built-in DOM element events, based on KO2 events but object-based
	public function clear_event($name, $callback = FALSE)
	{
		if ($callback === FALSE)
		{
			$this->_events[$name] = array();
		}
		elseif (isset($this->_events[$name]))
		{
			// Loop through each of the event callbacks and compare it to the
			// callback requested for removal. The callback is removed if it
			// matches.
			foreach ($this->_events[$name] as $i => $event_data)
			{
				list($callback, $args) = $event_data;
				
				if ($callback === $event_callback)
				{
					unset($this->_events[$name][$i]);
				}
			}
		}
	}
			
	public function run_event($name)
	{
		if ( ! empty($this->_events[$name]))
		{
			$callbacks  =  $this->get_event($name);

			foreach ($callbacks as $args)
			{
				list($callback, $data) = $args;

				$data = $data !== NULL ? $data : array($this, $name);
				call_user_func_array($callback, $data);
			}
		}

		// The event has been run!
		$this->_events_run[$name] = $name;
	}

	public function get_event($name)
	{
		return empty($this->_events[$name]) ? array() : $this->_events[$name];
	}

	public function has_run($name)
	{
		return isset($this->_events_run[$name]);
	}

	// Wrap this DOM element in another DOM element
	public function wrap($object)
	{
		$args = func_get_args($object);
		
		// If a bunch of args are given, build the DOM element from options
		if (count($args) > 1)
		{
			$object = call_user_func_array(array('DOM', 'factory'), $args);
		}
		
		// Make sure we are working with a proper DOM element
		if ( ! $object instanceOf DOM)
		{
			throw new Kohana_Exception('Wrapping object must be a DOM instance');
		}
		
		// Find the parent
		$parent = $this->parent();
		
		// If there isn't a parent, just swap $this with the wrapper
		if ( ! $parent)
		{
			$clone = clone $this;
			$object->add($clone);
			foreach (get_object_vars($object) as $key => $value)
			{
				$this->$key = $value;
			}
			
			return $this;
		}

		// Find the offset
		$offset = $parent->find_offset($this->attr('name'));
		// Set the wrapper's new parent
		$object->_parent = $parent;
		// Replace this element with the wrapper
		$parent->replace($offset, $object);
		// Add this element to the wrapper
		$object->add($this);

		return $this;
	}

	// Unwrap this DOM element
	public function unwrap()
	{
		// Find the offset
		$wrapper = $this->parent();
		$parent = $wrapper->parent();
		$offset = $parent->find_offset($wrapper->attr('name'));

		$parent->replace($offset, $wrapper->get());
	}

	// Return an array of elements[name] => val or find and return an element by its name
	public function get($field)
	{		
		foreach ($this->_elements as $vals)
		{
			if (strtolower($vals[0]) == strtolower($field))
				return $vals[1];
		}		
	}
	
	// Return array of element with its specified value
	public function as_array($value = NULL)
	{
		// Create the empty array to fill
		$array = array();
		foreach ($this->_elements as $vals)
		{
			list($name, $element) = $vals;
			
			if (in_array($value, $this->_attributes))
			{
				// If it's an attribute, use the attribute
				$array[$name] = $this->attr($value);
			}
			elseif ($value == 'style')
			{
				// Use _styles array if requested
				$array[$name] = $this->_styles;
			}
			else
			{
				// By default, return name => element
				$array[$name] = ($value === NULL) ? $element : $element->$value;
			}
		}
		
		return $array;
	}
	
	public function offset($offset)
	{
		// Return an element inside this element by offset
		return (isset($this->_elements[$offset])) ? $this->_elements[$offset] : FALSE;
	}
	
	// Determine the offset of an element by its name
	public function find_offset($search)
	{
		foreach ($this->_elements as $offset => $vals)
		{
			list($name, $element) = $vals;
			
			if ($search == $name)
				return $offset;
		}
	}
	
	// Turn attributes into a string (tag="val" tag2="val2")
	protected function attr_str()
	{
		if (empty($this->_attr) AND empty($this->_styles))
			return;
			
		$str = '';
		
		foreach ($this->_attr as $attr => $value)
		{
			$str.= ' '.$attr.'='.$this->_quote.$value.$this->_quote;
		}
		
		// Then attach styles
		if ($this->_styles)
		{
			$str.= ' style='.$this->_quote;
			foreach ($this->_styles as $style => $value)
			{
				$str.= $style.':'.$value.';';
			}
			$str.= $this->_quote;
		}
		
		return $str;
	}
	
	// Allows just the opening tag to be returned
	public function open($append_str = NULL)
	{
		$singletag = in_array($this->_tag, $this->_singles);

		$str = '<'.$this->_tag.$this->attr_str();
		$str.= ( ! $singletag) ? '>'."\n" : '';
		
		return $str;
	}
	
	// Allows just the closing tag to be returned
	public function close()
	{
		$singletag = in_array($this->_tag, $this->_singles);
		
		$str = ( ! $singletag) ? '<' : '';
		$str.= '/';
		$str.= ( ! $singletag) ? $this->_tag : '';
		$str.= '>'."\n";
		
		return $str;
	}
	
	// Return rendered element
	public function render($options = FALSE)
	{
		$singletag = in_array($this->_tag, $this->_singles);
		
		$str = $this->open();
	
		if ( ! $singletag)
		{
			$str.= $this->_text;
			foreach ($this->_elements as $vals)
			{
				list($name, $element) = $vals;
				$str.= $element->render($options);
			}
		}
		
		return $str.= $this->close();
	}
}