<?php

class Model_Through implements Serializable
{
	
	//private $_db				= NULL;		// 	Database connection
	
	private $_container_list		= array();	//	Through List [ EX List of friendships for friends ]
	private $_models_loaded			= array();	// 	Array of loaded objects
	
	private $_parent_object_id		= NULL;		// Parent object ID
	private $_parent_object_class 	= NULL;		// Parrent Class Nam
	
	private $_link_key				= NULL;		// 	Property of list element to create object off of
	private $_link_object			= NULL;		// 	Object to create from key
	
	private $_linked_key			= NULL;		// 	Property of list element to create object off of
	private $_linked_object			= NULL;		// 	Object to create from key
	
	
	
	public function __construct($parent_object, &$list, $object_type, $link_key = NULL)
	{
		if ($link_key == NULL)
		{
			$link_key = $object_type.'_id';
		}
		
		$this->_container_list 	= &$list;
		
		$this->_parent_object_id = $parent_object->id;
		$this->_parent_object_class = get_class($parent_object);
		
		//	Friendship
		$this->_link_object = $list->containsObject();
		$this->_link_key	= strtolower(get_class($parent_object)).'_id';
		// Friend
		$this->_linked_object	= $object_type;
		$this->_linked_key		= $link_key;
	}
	
	
	/**
	 * add()
	 * 
	 * Returns an array of instantiated objects
	 * 
	 * @param Object to add
	 * @return Array of objects
	 */
	public function add($object)
	{
		global $__memcache;
		
		$linker_obj		= 	$this->_link_object;
		$linker_key 	= 	$this->_link_key;
		$linked_key		=	$this->_linked_key;
		
		$link = $linker_obj::create( array( $linker_key => $this->_parent_object_id, $linked_key=>$object->id) );
		
		
		$did_save = $link->save();
		$this->_container_list->add($link->id);
		
		if ( $did_save == TRUE )
		{
			$__memcache->set($this->_parent_object_class.':find:first', NULL, 0);
			$__memcache->set($this->_parent_object_class.':find:last', NULL, 0);
			$__memcache->set($this->_parent_object_class.':find:all', NULL, 0);
			$__memcache->set($this->_parent_object_class.':where:array', NULL, 0);
		}
		
		return $object;
	}
	
	/**
	 * append()
	 * 
	 * Returns an array of instantiated objects
	 * 
	 * @param Object to add
	 * @return Array of objects
	 */
	public function append($object)
	{
		global $__memcache;
		
		$linker_obj		= 	$this->_link_object;
		$linker_key 	= 	$this->_link_key;
		$linked_key		=	$this->_linked_key;
		
		$link = $linker_obj::create( array( $linker_key => $this->_parent_object_id, $linked_key=>$object->id) );
		
		
		$did_save = $link->save();
		$this->_container_list->append($link->id);
		
		if ( $did_save == TRUE )
		{
			$__memcache->set($this->_parent_object_class.':find:first', NULL, 0);
			$__memcache->set($this->_parent_object_class.':find:last', NULL, 0);
			$__memcache->set($this->_parent_object_class.':find:all', NULL, 0);
			$__memcache->set($this->_parent_object_class.':where:array', NULL, 0);
		}
		
		return $object;
	}
	
	public function all()
	{
		$linker_objects = $this->_container_list->all();
		
		$result = array();
		$class_to_build 	= $this->_linked_object;
		$linker_property 	= $this->_linked_key;
				
		foreach($linker_objects as $obj)
		{
			if ( FALSE == isset($this->_models_loaded[$obj->linker_property]) )
			{
				$this->_models_loaded[$obj->$linker_property] = $result[$obj->$linker_property] = $class_to_build::_build($obj->$linker_property);
			}
			else
			{
				$result[$obj->$linker_property] = $this->_models_loaded[$obj->$linker_property];
			}
		}
				
		return array_values($result);
	}
	
	/** 
	 * contains()
	 * 
	 * Returns boolean whether the passed object is in the list or not
	 * 
	 * @return String the type of objects
	 */
	public function contains($object)
	{
		$same_type = ( strtolower($this->_linked_object) == strtolower(get_class($object)) );
		
		if ( TRUE == $same_type )
		{
			
			// Load all the objects. 
			$all = $this->all();
			
			// The models loaded is indexed by the forigen key, so by searching on that, we can tell if
			// the object is in our list
			if ( TRUE == isset($this->_models_loaded[$object->id]) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * count()
	 * 
	 * Returns a count of the objects in this relation
	 * 
	 * @return Int Number of objects in the relation
	 */
	public function count()
	{
		return $this->_container_list->count();
	}
	
	/**
	 * get(number)
	 * 
	 * Returns the object number $number in the list
	 * 
	 * @return Object
	 */
	public function get($number)
	{
		$obj = $this->_container_list->get($number);
		
		$result = array();
		$class_to_build 	= $this->_linked_object;
		$linker_property 	= $this->_linked_key;
		
		if ( FALSE == isset($this->_models_loaded[$obj->$linker_property]) )
		{
			$this->_models_loaded[$obj->$linker_property] = $class_to_build::_build($obj->$linker_property);
		}
		return $this->_models_loaded[$obj->$linker_property];
	}
	
	public function container_list()
	{
		return $this->_container_list;
	}
	
	/**
	 * removeIndex()
	 * 
	 * Removes the object passed from the list
	 * 
	 * @param Int index to remove
	 * @return Array of objects
	 */
	public function remove($object)
	{
		return $this->_container_list->remove($object);
	}
	
	
	/**
	 * removeIndex()
	 * 
	 * Removes the object at the specified Index
	 * 
	 * @param Int index to remove
	 * @return Array of objects
	 */
	public function removeIndex($index)
	{
		return $this->_container_list->removeIndex($index);
	}
	
	/** 
	 * clear()
	 * 
	 * Removes all elements in the list
	 * 
	 */
	public function clear()
	{
		
		foreach($this->_container_list as $k=>$v)
		{
			$this->_container_list->removeIndex($k);
		}
		$this->_models_loaded = NULL;
		
		return ($this->_container_list->count() == 0);
	}
	
	/**
	 * save()
	 * 
	 * Saves any updates to this list to the database
	 * 
	 * @return Boolean success
	 */
	public function save()
	{
		// :TODO: ... Is there anything that needs to be saved? maybe save all changes to the modified objects?
	}
	
	
	
	
	/* Abstract methods for Serializable 
	 *   Removes PDO instance from object serialize call.
	 *   Allowing the object to be stored and cached
	 */
	public function serialize()
	{
		
		return serialize(array($this->_container_list, $this->_models_loaded, $this->_linker_key, $this->_linker_object));
	
	}

	/* Abstract methods for Serializable 
	 *   Restores all object data from serialized string
	 */
	public function unserialize($serialized)
	{
		
		list($this->_container_list, $this->_models_loaded, $this->_linker_key, $this->_linker_object) = unserialize($serialized);
		// Restore the database property with new connection
		//$this->_db = Model::_getDatabase();
		
	}
}