<?php

class Model_List implements Serializable
{
	
	//private $_db				= NULL;		// 	Database connection
	
	private $_id_list 			= array();	//	Array of IDs 
	private $_models_loaded		= array();	// 	Array of loaded objects
	private $_contains_object	= NULL;		//	Name of object IDs point to
	private $_cursor			= -.5;		// 	Position between
	private $_count				= 0;		//	Count of IDs in _id_list
	private $_last_returned		= NULL;		//	Last returned offset in id list. This is used for the remove method.
	private $_for_object		= NULL;		//	Name of object IDs are associated to
	
	
	
	public function __construct($type, $ids, $object = NULL)
	{
		$this->_id_list 		= $ids;
		$this->_contains_object	= $type;
		$this->_count			= count($ids);
		$this->_db 				= Model::_getDatabase();
		$this->_for_object		= $object;
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
		if ( is_object($object) )
		{
			$id = $object->id;
		}
		else
		{
			$id = $object;
		}
		$at = max(0, ceil($this->_cursor)-1);
		
		array_splice($this->_id_list, $at, 0, $id);
		$this->_cursor++;
		$this->_count++;
		return $object;
	}
		
	/**
	 * all()
	 * 
	 * Returns an array of instantiated objects
	 * 
	 * @return Array of objects
	 */
	public function all()
	{
		$class_name = $this->_contains_object;
		foreach($this->_id_list as $i=>$id)
		{
			if ( FALSE == isset($this->_models_loaded[$i]) )
			{
				$this->_models_loaded[$i] = $class_name::find($id);
			}
		}
		$object_array = array_values($this->_models_loaded);
		return $object_array;
	}
	
	/**
	 * add()
	 * 
	 * Returns an array of instantiated objects
	 * 
	 * @param Object to add
	 * @return Array of objects
	 */
	public function append($object)
	{
		if ( is_object($object) )
		{
			$id = $object->id;
		}
		else
		{
			$id = $object;
		}
		$this->_id_list[] = $id;
		$this->_count++;
		return TRUE;
	}
	
	/** 
	 * count()
	 * 
	 * Empties the list
	 * 
	 * @return Bool success
	 */
	public function clear()
	{
		foreach($this->_id_list as $k=>$v)
		{
			$this->removeIndex($k);
		}
		return ($this->_count == 0);
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
		$same_type = ( strtolower($this->containsObject()) == strtolower(get_class($object)) );
		
		if ( TRUE == $same_type )
		{
			if ( TRUE == in_array($object->id, $this->_id_list) )
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
			echo ( strtolower($this->containsObject()) .' == '. strtolower(get_class($object)) );
			return FALSE;
		}
	}
	
	/** 
	 * contains()
	 * 
	 * Returns the type of object the list contains
	 * 
	 * @return String the type of objects
	 */
	public function containsObject()
	{
		return $this->_contains_object;
	}
	
	/** 
	 * count()
	 * 
	 * Returns the number of objects in this list
	 * 
	 * @return Int the number of objects
	 */
	public function count()
	{
		return $this->_count;
	}
	
	/**
	 * first()
	 * 
	 * Returns the first object
	 * 
	 * @return Object
	 */
	public function first()
	{
		$array =  $this->limit_offset(1,0);
		if ( count($array) >= 1 )
		{
			return $array[0];
		}
		else
		{
			return NULL;
		}
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
		if ( isset($this->_id_list[$number]) && $this->_id_list[$number] != NULL )
		{
			if ( FALSE == isset($this->_models_loaded[$number]) )
			{
				$class_name = $this->_contains_object;
				$this->_models_loaded[$number] = $class_name::find($this->_id_list[$number]);
			}
			return $this->_models_loaded[$number];
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * hasNext()
	 * 
	 * Returns boolean if there is a next object
	 * 
	 * @return Boolean
	 */
	public function hasNext()
	{
		return ( floor($this->_cursor) < $this->_count );
	}
	
	/**
	 * hasPrevious()
	 * 
	 * Returns boolean if there is a previous object
	 * 
	 * @return Boolean
	 */
	public function hasPrevious()
	{
		return ( floor($this->_cursor) > 0 );
	}
	
	/**
	 * ids()
	 * 
	 * Returns the list of IDs
	 * 
	 * @return Array of ids
	 */
	public function ids()
	{
		return $this->_id_list;
	}
	
	/**
	 * last()
	 * 
	 * Returns the last object in the array
	 * 
	 * @return Object
	 */
	public function last()
	{
		$array =  $this->limit_offset(1,-1);
		return $array[0];
	}
	
	/**
	 * limit()
	 * 
	 * Returns the list of IDs
	 * 
	 * @return Array of objects
	 */
	public function limit($number)
	{
		$object_array = array();
		$class_name = $this->_contains_object;
		$id_list = array_slice($this->_id_list, 0, $number);
		foreach($id_list as $i=>$id)
		{
			if ( isset($this->_models_loaded[$i]) )
			{
				$object_array[] = &$this->_models_loaded[$i];
			}
			else
			{
				$object_array[] = $this->_models_loaded[$i] = $class_name::find($id);
			}
		}
		return $object_array;
	}
	
	/**
	 * limit()
	 * 
	 * Returns the list of IDs
	 * 
	 * @return Array of objects
	 */
	public function limit_offset($limit, $offset)
	{
		$object_array = array();
		$class_name = $this->_contains_object;
		$id_list = array_slice($this->_id_list, $offset, $limit);
		foreach($id_list as $i=>$id)
		{
			if ( isset($this->_models_loaded[$i]) )
			{
				$object_array[] = &$this->_models_loaded[$i];
			}
			else
			{
				$object_array[] = $this->_models_loaded[$i] = $class_name::find($id);
			}
		}
		return $object_array;
	}
	
	/**
	 * next()
	 * 
	 * Returns next Object
	 * 
	 * @return Object
	 */
	public function next()
	{
		$class_name = $this->_contains_object;
		if ( $this->hasNext() )
		{
			$this->_last_returned = ceil($this->_cursor);
			if ( TRUE == isset($this->_models_loaded[$this->_last_returned]) )
			{
				$obj = &$this->_models_loaded[$this->_last_returned];
			}
			else
			{
				$obj = $this->_models_loaded[$this->_last_returned] = $class_name::find($this->_id_list[$this->_last_returned]);
			}
			$this->_cursor++;
			return $obj;
		}
		else
		{ 
			return NULL;
		}
	}
	
	/**
	 * limit()
	 * 
	 * Returns the list of IDs
	 * 
	 * @return Array of objects
	 */
	public function offset($number)
	{
		$object_array = array();
		$class_name = $this->_contains_object;
		$id_list = array_slice($this->_id_list, $number, 0);
		foreach($id_list as $i=>$id)
		{
			if ( isset($this->_models_loaded[$i]) )
			{
				$object_array[] = &$this->_models_loaded[$i];
			}
			else
			{
				$object_array[] = $this->_models_loaded[$i] = $class_name::find($id);
			}
		}
		return $object_array;
	}
	
	/**
	 * previous()
	 * 
	 * Returns previous Object
	 * 
	 * @return Object
	 */
	public function previous()
	{
		$class_name = $this->_contains_object;
		if ( $this->hasPrevious() )
		{
			$this->_last_returned = floor($this->_cursor-1);
			if ( TRUE == isset($this->_models_loaded[$this->_last_returned]) )
			{
				$obj = &$this->_models_loaded[$this->_last_returned];
			}
			else
			{
				$obj = $this->_models_loaded[$this->_last_returned] = $class_name::find($this->_id_list[$this->_last_returned]);
			}
			$this->_cursor--;
			return $obj;
		}
		else
		{ 
			return NULL;
		}
	}
	
	
	
	
	/**
	 * remove()
	 * 
	 * Removes the object at the cursor
	 * 
	 * @return Array of objects
	 */
	public function remove($arg = NULL)
	{
		if ( TRUE == is_object($arg) )
		{
			return $this->removeObject($arg);
		}
		elseif ( TRUE == is_numeric($arg) )
		{
			return $this->removeIndex($arg);
		}
		else
		{
			return $this->clear();
		}
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
		if ( FALSE != isset($this->_id_list[$index]) )
		{
			if ( TRUE == isset($this->_models_loaded[$index]) )
			{
				$this->_models_loaded[$index]->delete();
			}
			else
			{				
				$class_name = $this->_contains_object;
				$obj = $class_name::find($this->_id_list[$index]);
				$obj->delete();
			}
			unset($this->_id_list[$index]);
			unset($this->_models_loaded[$index]);
			$this->_count--;
			$this->_cursor = min($this->_cursor, $this->_count-.5);
			return TRUE;
		}
		return FALSE;
	}
	
	
	/**
	 * removeObject()
	 * 
	 * Removes the object passed
	 * 
	 * @return Boolean success
	 */
	public function removeObject($obj)
	{
		if ( FALSE == $this->contains($obj) )
		{
			return FALSE;
		}
		else
		{
			$index = array_search($obj->id, $this->_id_list);
			if ( FALSE == $index ){
				return FALSE;
			}
			else
			{
				return $this->removeIndex($index);
			}
		}
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
		
		
	}
	
	
	
	
	/* Abstract methods for Serializable 
	 *   Removes PDO instance from object serialize call.
	 *   Allowing the object to be stored and cached
	 */
	public function serialize()
	{
		
		return serialize(array($this->_id_list, $this->_contains_object, $this->_count));
	
	}

	/* Abstract methods for Serializable 
	 *   Restores all object data from serialized string
	 */
	public function unserialize($serialized)
	{
		
		list($this->_id_list, $this->_contains_object, $this->_count) = unserialize($serialized);
		// Restore the database property with new connection
		//$this->_db = Model::_getDatabase();
		
	}
}