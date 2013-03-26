<?php

class Genre extends model{
	
	static $has_many = array('book');
	
	static $validates_presence_of = array('name');
	static $validates_uniqueness_of = array('name');
	
}