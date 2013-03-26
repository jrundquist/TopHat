<?php

class User extends Model{
	
	static $has_one = array(
							'profile'
							);
							
	static $has_many = array(
							'friends'=>array(
								'through'=>'friendships', 
								'class'=>'user'),
							'friendships'
							);
	
	static $validates_presence_of = array('username', 'password', 'email');
	static $validates_uniqueness_of = array( 'username', 'email' );
	static $validates_pattern_of = array('password'=>'[0-9A-Za-z]{4,}', 'email'=>'^[A-Za-z0-9\._\%\+\-]+@[A-Za-z0-9\.\-]+\.[A-Za-z]{2,4}$');
	static $validates_length_of = array('username'=>array('min'=>4, 'max'=>30));
	
	static function onSavePassword($password){
		return md5($password);
	}
	
}