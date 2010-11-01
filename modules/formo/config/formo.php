<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default'	=> array
	(
		// Automatically add posts prior to rendering
		'auto_post'					=> FALSE,
		// File name for validation messages
		'validate_messages_file'	=> 'validate',
		// Always render fields in views unless specifically stated
		'render_as_views'			=> FALSE,
	
		// Translate labels
		'translate_labels'			=> FALSE,
		// Translate validation messages
		'translate_messages'		=> TRUE,
		// Default settings for fields
		'defaults'					=> array
		(
			'password'	=> array
			(
				'_driver'	=> 'password'
			),
		),
	),
	
	/* Groups examples
	 *
	 * These are definitions for autoloading with add_group()
	 *
	*/
	'groups'	=> array
	(
		// Standard address fields
		'address'	=> array
		(
			'street'	=> array
			(
				'label'		=> 'Street',
			),
			'street2'	=> array
			(
				'label'		=> 'Street 2',
				'required'	=> FALSE,
			),
			'city'		=> array
			(
				'label'		=> 'City',
			),
			'state'		=> array
			(
				'label'		=> 'State',
				'_driver'	=> 'select',
				'blank'		=> array(0),
				'_values'	=> array
				(
					'UT'	=> 'Utah',
					'CA'	=> 'California',
					20		=> 'Word up',
				),
				'rules'		=> array
				(
					'not_empty'	=> NULL,
				),
			),
			'zip'		=> array
			(
				'label'		=> 'Zip',
			),
		),
		
		// Standard password fields
		'password'	=> array
		(
			'password'			=> array
			(
				'_driver'	=> 'password',
				'label'		=> 'Password',
			),
			'password_confirm'	=> array
			(
				'_driver'	=> 'password',
				'label'		=> 'Confirm Password',
				'rules'		=> array
				(
					'matches[password]' => array()
				),
			),
		),
	),
	
	
	
	/* Example of definitions for forms by type
	 *
	 *
	 *
	 'types'	=> array
	 (
	 	'json'	=> array
	 	(
	 		'_type'	=> 'json',
	 	)
	 ),
	*/	
);