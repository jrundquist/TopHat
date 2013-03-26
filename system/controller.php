<?php
/**
* Class extended by all Controllers
*
* This class contains the basic functionality and properties of a
*	controller class. Any controllers used by the application
* 	should extend this class. The methods defined here are expected
* 	to be present in each controller. If they are not, errors may
* 	arise.
*
*
* @category SystemClasses
* @package TopHat
* @author James Rundquist james.k.rundquist@gmail.com
* @version Release: 1.0.0
* @since Class available since Release 1.0.0
*/

class controller {

	public $logged_in = false;
	public $user = null;
	public $callbutton = null;
	public $parameters = array();
	public $method = 'GET';

	function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
	}

}