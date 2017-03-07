<?php
namespace Application;

return array(

	/**
	 * define objects and fields to include in global search
	 * example:
	 * 'search_objects' => [
	 		PHPObjectName' => [ 
				'route' => 'route/action',	-- id is currently used as the route parameter by default
				'fields' => ['fieldName1', 'fieldName2', 'fieldName3', ...],
				'icon' => 'fa-iconclass', -- fontawesome class name
				'displayObject' => 'ObjectName',
				'displayFields' => ['Field One', 'Field Two', 'Field Three', ...],
			],
		]
	 */
	'global_search' => [
		'show_search' => true,
		'search_namespace' => null,
		'search_input_prompt' => 'Enter Search',
		'search_objects' => [
			'User' => [
				'route' => 'users/view',
				'fields' => ['title', 'firstName', 'lastName', 'email', 'username'],
				'displayFields' => ['Title', 'Forename', 'Surname', 'Email Address', 'Username'],
				'displayObject' => 'User',
				'icon' => 'fa fa-user',
			],
		],
	],

);
