<?php

require_once('../bootstrap.php');

class Profile {
	public $id = 4;
}

$GLOBALS['routes'] = array(		
	'/' => 'home:index',
	'/profiles' => 'profile',
	'/profiles/create-profile' => 'profile:create',
	'/profiles/create-account' => 'profile:create_account',
	'/profiles/(profile)' => 'profile:view',
	'/profiles/(profile)/[action]' => 'profile',
	
	'/search/(profile)/:page' => 'search:view',
	
	'/test/[action]'=>'test'
);



class RoutesTest extends PHPUnit_Framework_TestCase
{

	public function testLinkTo()
    {
		$this->assertEquals( '/' , 							Router::linkTo('home', 'index') );
		$this->assertEquals( '/test/test1' , 				Router::linkTo('test', 'test1') );
	
		$this->assertEquals( '/profiles/create-profile' , 	Router::linkTo('profile', 'create') );
		$this->assertEquals( '/profiles' , 					Router::linkTo('profile') );
		
		$objects = array('profile'=>new Profile() );
		
		$this->assertEquals( '/profiles/4' , 				Router::linkTo('profile', 'view', array(), $objects ) );
		
		$this->assertEquals( '/search/4/1' , 				Router::linkTo('search', 'view', array('page'=>1), $objects) );
    }
}