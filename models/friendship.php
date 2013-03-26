<?php


class Friendship extends Model{
	
	static $belongs_to = array(
							'friend'=>array(
								'class'=>'user'
											)
							);
	static $validates_uniqueness_of = array( array( 'user_id', 'friend_id' ) );
	static $validates_pattern_of = array('user_id'=>'[0-9]+', 'email'=>'[0-9]+');
	
}