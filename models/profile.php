<?php

class Profile extends Model{
	
	static $belongs_to = array(
							'user'
							);
	
	static $validates_presence_of = array( );
	static $validates_uniqueness_of = array( );
	static $validates_pattern_of = array( 'gender'=>'[MF]{1}' );
	
}