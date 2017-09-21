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
		'adapter' => 'GlobalSearch\Adapter\PropelAdapter',
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

    // partial and view helper reference
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'partial/global-search-results' => __DIR__ . '/../view/partial/global-search-results.phtml',
            'partial/global-search-ui' => __DIR__ . '/../view/partial/global-search-ui.phtml',
        ),
    ) ,
);