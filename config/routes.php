<?php

/**
 * Router's routes file
 *
 *	This file contains all the mappings from URLs to controllers and actions
 * 
 *	Routes are defined as either fully qualified and mapped to a function, or ambiguously
 *		mapped to a controller.
 *
 *	For each route, if the controller does not respond to that action a view will be looked
 *		for of the name name as the action. If found it will be rendered.
 * 
 *
 * 	SPECIAL PARAMETERS
 * 		Using a semicolon in front of any qualifier takes that portion of the URL and passes it
 *   		as a variable in the parameters[] array in the controller object
 * 		  EX. /paginated-something/:page  ~  /paginated-something/2 
 *			  would have the parameters['page'] set to '2'	
 *
 * 		Using [action] will map that portion of the URL to the coinciding method in the controller
 *   		object
 * 		  EX. /test/[action] => 'testing' ~  /test/test-all
 * 			  would run the test_all method in the testing object
 *
 * 		Using parentheses around an object name will try and create a new instance of that object 
 *   		passing that portion of the URL to the constructor of the object. If the object's is_valid
 *   		parameter is FALSE after the object is instantiated the URL will fail, continuing to the next 
 *   		matching route. 
 * 		  EX. /profiles/(profile)  ~  /profiles/testerMan 
 * 			  would create a new Profile object passing testerMan as the parameter to the constructor
 * 			  if after instantiation the object's is_valid property is not TRUE, the route will not match
 *			  and the router will just continue on
 */
$GLOBALS['routes'] = array(		
	
	'/' => 'home:index',
	
	'/profiles' => 'profile',
	'/profiles/create-profile' => 'profile:create',
	'/profiles/create-account' => 'profile:create_account',
	'/profiles/(profile)' => 'profile:view',
	'/profiles/(profile)/[action]' => 'profile',
	
	'/test/[action]'=>'test'
	
	
);