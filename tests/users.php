<?php
require_once('../bootstrap.php');
require_once('../helpers/utility.php');
require_once('../system/model.php');
require_once('../system/model_list.php');

require_once('../models/user.php');
require_once('../models/friendship.php');

global $__memcache;


class UserTest extends PHPUnit_Framework_TestCase
{
	
	public $user = NULL;
	
    protected function setUp()
    {
		global $__memcache;

		if (FALSE == is_object($__memcache))
		{
			$__memcache = new Memcached();
			$__memcache->addServer( 'localhost', 11211 );
			$__memcache->flush();
		}
		
		if (FALSE == is_object($this->user))
		{
			$this->user = User::find( array('username'=>'james', 'password'=>'pass', 'limit'=>1) );
		}
    }
 
    protected function tearDown()
    {
        // VOID
    }

	public function testSetupSuccess()
    {
		$this->assertEquals( TRUE, is_object($this->user) );
		$this->assertEquals( 'User', get_class($this->user) );
		$this->assertEquals( 'james', $this->user->username );
		$this->assertEquals( TRUE, $this->user->isValid() ); 
    }

	/**
     * @depends testSetupSuccess
     */
	public function testFriendships()
	{
		$this->assertEquals( TRUE, is_object($this->user->friendships) );
		$this->assertEquals( 'Model_List', get_class($this->user->friendships) );
		$this->assertEquals( 2, $this->user->friendships->count() );
		$this->assertEquals( 'Friendship', get_class($this->user->friendships->first()) );
		
		$this->assertEquals( TRUE, $this->user->friendships->clear() );
		$new_friend_one = Friendship::create( array('user_id'=>1, 'friend_id'=>5) );
		$this->assertEquals( TRUE, $new_friend_one->save() );
		$this->assertEquals( $new_friend_one, $this->user->friendships->add($new_friend_one) );
		$this->assertEquals( 1, $this->user->friendships->count() );
		
		$new_friend_two = Friendship::create( array('user_id'=>1, 'friend_id'=>4) );
		$this->assertEquals( TRUE, $new_friend_two->save() );
		$this->assertEquals( $new_friend_two, $this->user->friendships->add($new_friend_two) );
		$this->assertEquals( 2, $this->user->friendships->count() );
	}
	
	/**
     * @depends testFriendships
     */
	public function testFriends()
	{
		$this->assertEquals( 'Model_Through' , get_class($this->user->friends) );
		$this->assertEquals( TRUE , is_array($this->user->friends->all()) );
	}
	
	/**
     * @depends testFriends
     */
	public function testAddFriend()
	{
		$this->assertEquals( 2 , $this->user->friends->count() );
		
		$new_friend = User::find( 3 );
		$this->assertEquals( 'User', get_class($new_friend) );
		$this->assertEquals( 3,	$new_friend->id );
		
		$this->assertEquals( $new_friend, $this->user->friends->add($new_friend) );
		$this->assertEquals( 3 , $this->user->friends->count() );
		//$this->assertEquals( 3 , $this->user->friends->get(2)->id );
	}
	
	/**
     * @depends testAddFriend
     */
	public function testRemoveFriend()
	{
		$this->assertEquals( 3 , $this->user->friends->count() );
		$this->assertEquals( TRUE, $this->user->friends->removeIndex(2) );
		$this->assertEquals( 2 , $this->user->friends->count() );
	}


	/**
     * @depends testRemoveFriend
     */
	public function testFriendshipVsFriend()
	{
		//Are they equal
		$this->assertEquals( $this->user->friends->container_list(), $this->user->friendships );
		
		//Make a new friend
		$new_friend = User::find( 3 );
		
		//Is the add successful
		$this->assertEquals( $new_friend, $this->user->friends->append($new_friend) );
		
		//Are they equal
		$this->assertEquals( $this->user->friends->container_list(), $this->user->friendships );
		$this->assertEquals( 3 , $this->user->friends->count() );
		$this->assertEquals( 3, $this->user->friendships->count() );

		//Remove the friend
		$this->assertEquals( TRUE, $this->user->friends->removeIndex(2) );
		$this->assertEquals( 2 , $this->user->friends->count() );
		$this->assertEquals( 2, $this->user->friendships->count() );

	}
	
	public function testFriendshipPermanence()
	{	
		$this->user->friendships->get(0)->user_id = 100;
		$this->assertEquals( TRUE, $this->user->friendships->get(0)->isChanged() );
	}
	
	public function testFriendPermanence()
	{	
		$this->assertEquals( TRUE, $this->user->friends->get(0)->username = 'something_new' );
		$this->assertEquals( TRUE, $this->user->friends->get(0)->hasChanged('username') );
	}
	
	/**
     * @depends testAddFriend
     */
	public function testFriendsContains()
	{	
		//Check for someone we know to be a friend
		$possible_friend = User::find( 4 );
		$this->assertEquals( TRUE, $this->user->friends->contains($possible_friend) );
		
		// Check for someone we know not to be a friend
		$possible_friend_not = User::find( 3 );
		$this->assertEquals( FALSE, $this->user->friends->contains($possible_friend_not) );
		
	}
}