<?php

class Book extends Model{
	static $belongs_to = array('author', 'genre');
	static $has_one = array();
	//static $has_many = array();
	
	static $validates_presence_of = array('title', 'author');
	static $validates_uniqueness_of = array( array('title', 'author'), 'upc' );
	static $validates_pattern_of = array('upc'=>'[a-f0-9]{8}');
	
	
	
	public static function getUpc($current_value){
		return strtoupper($current_value);
	}
	
	public static function setUpc($new_value){
		return preg_replace('/[^0-9a-f]/', '', strtolower($new_value));
	}
		
}