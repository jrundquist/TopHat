<?php
/**
*
* This class provides an easy way to make and manage form elements
*
*
* @category SystemUtilities
* @package TopHat
* @author James Rundquist james.k.rundquist@gmail.com
* @version Release: 1.0.0
* @since Class available since Release 1.0.0
*
*/

class Form {

	private $name 		= '';
	private $action 	= '';
	private $method 	= 'POST';
	private $enctype 	= '';
	private $on_submit 	= '';
	private $class 		= '';
	private $id 		= '';



	public function __construct()
	{
		return true;
	}

	public function create($name='form', $options = array())
	{
		$this->name = $name;
		foreach ($options as $option=>$value)
		{
			if ( TRUE == property_exists(self, $option) )
			{
				$this->$option = $value;
			}
			elseif ( $option == 'file' )
			{
				$this->enctype = 'multipart/form-data';
			}
		}
		return true;
	}

	public function start()
	{

		$html = '<form ';

		$html .= 'name="'.$this->name.'" ';
		$html .= 'method="'.$this->method.'" ';
		$html .= 'action="'.$this->action.'" ';
		$html .= 'onSubmit="'.$this->on_submit.'" ';

		if ( '' != $this->enctype )
		{
			$html .= 'enctype="'.$this->enctype.'" ';
		}
		if ( '' != $this->on_submit )
		{
			$html .= 'enctype="'.$this->on_submit.'" ';
		}
		if ( '' != $this->class )
		{
			$html .= 'class="'.$this->class.'" ';
		}
		if ( '' != $this->id )
		{
			$html .= 'class="'.$this->id.'" ';
		}
		$html .= '>';

		return $html;
	}

	public function end()
	{
		return '</form>';
	}
}