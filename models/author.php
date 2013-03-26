<?php

class Author extends Model{
	static $has_many = array('books');
	
	static $validates_presence_of = array('first_name', 'last_name', 'pen_name');
	static $validates_uniqueness_of = array( array('first_name', 'last_name', 'pen_name') );
	static $validates_pattern_of = array('born'=>'[0-9]{4}', 'died'=>'[0-9]{4}|NA');
}