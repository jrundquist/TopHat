<?php
/**
* Class extended by all Models
*
* This class provides the basic functionality of the ORM needed
* 	to run the site's objects. This class should be extended by all
*	models that relate to database objects. By implementing
*	serializable these objects can be stored in their present state
*	retrieved properly through serialization.
*
*
* @category SystemClasses
* @package TopHat
* @author James Rundquist james.k.rundquist@gmail.com
* @copyright 2010-2011 James Rundquist
* @version Release: 1.0.0
* @since Class available since Release 1.0.0
*/

class Model implements Serializable
{

	public $_class 				= FALSE;	// 	String used to keep the class name
	public $_is_new 			= FALSE;	//	Boolean used to keep track of if this is a new instance (not in DB)
	public $_valid_errors		= array(); 	//	Used to store the validation error data
	public $_data 				= array();	//	Stores working copy of database information
	public $_data_orig 			= array();	//	Stores the origional data (in the database)
	public $_columns 			= array();	// 	Array of the columns in the database
	public $_table 				= FALSE;	//	Table associated with model
	public $_db 				= NULL;		//	Database connection

	private $_raw_mode			= FALSE; 	// 	If the object is in Raw Mode the properties will not be passed through overridden getters and setters
											//		This mode is mainly used by the save, build, and validate methods to access raw database values.

	private $_belongs_to		= array();	// 	Array to track objects this belongs to
	private $_has_many			= array();	//	Array to track things this object has many of
	private $_has_one			= array();	//	Array to track things this object has one of

	public $_belongs_to_objects	= array();	//	Array to store all the many_objects containers
	public $_has_many_objects	= array();	//	Array to store all the many_objects containers
	public $_has_one_objects	= array();	//	Array to store all the many_objects containers


	/**
	 * _getDatabase()
	 *
	 * Returns a new instance of a PDO object connected to the database
	 * using the credentials set in the /config/config.php file
	 *
	 * @return PDO object
	 */
	public static function _getDatabase()
	{
		$db = NULL;

		$database_name = DB_DATA;

		$class_name = get_called_class();
		if( property_exists($class_name, 'database') ){
			$database_name = $class_name::$database;
		}

		try
		{
			$db = new PDO('mysql:dbname='.$database_name.';host='.DB_HOST, DB_USER, DB_PASS);
		}
		catch(PDOException $e)
		{
			include('views/errors/database.php');
			die();
		}
		return $db;
	}

	/**
	 * _setTable()
	 *
	 * Sets the table to the specified value
	 *
	 * @param String table
	 */
	public function _setTable($value)
	{
		$this->_table = $value;
	}

	/** _setRawMode()
 	 *
 	 *	Set whether to look for getter/setter overrides in object files
 	 *
 	 */
	public function _setRawMode($raw = FALSE)
	{
		// Set the raw_mode property to the passed value
		$this->_raw_mode = $raw;
	}

	/**
	 * _build
	 *
	 * Grabs an object of a certain type from the database filled with the
	 * information stored in the database with the id passed
	 *
	 * @param int ID of object in database
	 * @return Object
	 **/
	public static function _build($id = FALSE)
	{
		// Get the profiler and memcach instances
		global $__profiler, $__memcache;

		// Tell the profiler we are building an object
		$__profiler->enterSection('Model::_build');

		// What kink of object are we working with?
		$class_name = get_called_class();

		// Make the unique key that describes this object
		$key = $class_name.':id:'.$id;
		// Look to see if we have cached this instance of the model
		$result = $__memcache->get($key);

		if($result != NULL)
		{
			// We have a result :D
			// VOID -- the result will be returned
		}
		else
		{
			// We did not find the model in the cache
			// Create a new one
			$class = new $class_name;

			// If we were passed an ID, then get that instance from the tables
			if (FALSE != $id)
			{
				$sql = 'SELECT * FROM '.$class->_table.' WHERE id = :id LIMIT 1;';
				$select = $class->_db->prepare($sql);
				$select->bindValue('id',$id);
				$select->execute();
				$result_array = $select->fetch();
				// Set the mode to raw so we can access all the properties directly
				$class->_setRawMode(TRUE);
				foreach($class->_columns as $col)
				{
					// Assign each column in the database to it's property
					$class->$col = $result_array[$col];
				}
				// Return the mode to not-raw so accessors will pass through any overloaded methods again
				$class->_setRawMode(FALSE);
				// Set this objects intitial state [for comparison in hasChanged() and changed()]
				$class->_setInitial();
			}
			// Cache the object we just created
			$__memcache->set($key, $class, CACHE_OBJ_EXPIRE);

			// Set the result equal to the instance we just created
			$result = $class;
		}

		// Tell the profiler we are leaving the section
		$__profiler->leaveSection('Model::_build');
		// Return the object we just created
		return $result;
	}

	/**
	 * find()
	 *
	 * Returns the object based on the parameters passed
	 *
	 * @param id?
	 * @param 'all'|'first'|'last'
	 * @return an object or an array of objects
	 */
	public static function find()
	{

		// Get the profiler and memcach instances
		global $__profiler, $__memcache;

		// Tell the profiler we are entering the find section
		$__profiler->enterSection('Model::find');

		// What object are we working with?
		$class_name = get_called_class();

		// Create the base cache key.
		$key = $class_name.':find:';

		// Set the result to a default of NULL
		$result = NULL;

		// Count the number of arguments passed to us
		$args_count = count(func_get_args());

		if ( 'all' == strtolower((string)func_get_arg(0)) )
		{
			// If we were passed the string 'all'

			// This is a cachable search so we will check to see if we have it cached
			$result = $__memcache->get($key.'all');

			if ($result != NULL)
			{
				// If we found one in the cache

				// VOID -- we have the result and it will be returned

			}
			else
			{
				// We do not have find(all) cached

				// Search non-selectively for all IDs
				$ids = $class_name::where();

				// Set the results to an array instace
				$result = array();
				foreach($ids as $id)
				{
					// For each ID we found in the where, build an object of
					// 	that ID and add it to the result array
					$result[] = $class_name::_build($id);
				}

				// Store the result in the cache for faster reference in the future
				$__memcache->set($key.'all', $result, CACHE_OBJ_EXPIRE_LONG);
			}
		}
		else if ( 1 == $args_count && FALSE == is_array(func_get_arg(0)) && FALSE != is_numeric(func_get_arg(0)))
		{
			// If we were just passed an ID number

			// The ID is the argument
			$id = (int)func_get_arg(0);

			// The result is the instance of that object
			$result = $class_name::_build($id);

		}
		else if ( FALSE == is_array(func_get_arg(0)) && 'first' == strtolower(func_get_arg(0)) )
		{
			// If we were passed the string 'first'

			// This is a cachable search so we will check to see if we have it cached
			$result = $__memcache->get($key.'first');

			if ($result != NULL)
			{
				// The object is in the cache

				// VOID -- we have the result and it will be returned

			}
			else
			{
				// Th object was not cached

				// Find the id of the first object ordered by ID ASC [ ie 1, 2, 3, 5, 6, 9 ... etc ]
				$id = $class_name::where(array('limit'=>'1', 'order'=>array('by'=>'id', 'type'=>'ASC')) );

				// Create an instance of that object with the ID found
				$result = $class_name::_build($id);

				// Store the result
				$__memcache->set($key.'first', $result, CACHE_OBJ_EXPIRE_LONG);
			}

		}
		else if ( FALSE == is_array(func_get_arg(0)) && 'last' == strtolower(func_get_arg(0)) )
		{
			// If we were passed the string 'first'

			// This is a cachable search so we will check to see if we have it cached
			$result = $__memcache->get($key.'last');

			if ($result != NULL)
			{

				// VOID -- we have the result and it will be returned

			}
			else
			{
				// Th object was not cached

				// Find the id of the first object ordered by ID DESC [ ie 9, 8, 5, 3 ... etc ]
				$id = $class_name::where(array('limit'=>'1', 'order'=>array('by'=>'id', 'type'=>'DESC')) );

				// Create an instance of that object with the ID found
				$result = $class_name::_build($id);

				// Store the result
				$__memcache->set($key.'last', $result, CACHE_OBJ_EXPIRE_LONG);
			}

		}
		else if ( is_array(func_get_arg(0)) )
		{
			// Passed search chriteria

			// Find the IDs matching the chriteria
			$ids = $class_name::where(func_get_arg(0));

			if ( FALSE == is_array($ids) )
			{
				// If we were returned a single result
				$result = $class_name::_build($ids);
			}
			else
			{
				// Results is now an array
				$result = array();
				foreach($ids as $id)
				{
					// Foreach ID add an object of the proper type to the aray result instanciated with the ID
					$result[] = $class_name::_build($id);
				}
			}
		}

		// Tell the profiler we are leaving the section
		$__profiler->leaveSection('Model::find');

		// Return out result
		return $result;
	}

	/**
	 *	where()
	 *
	 *	Returns an array of IDs matching the passed parameters
	 *
	 * @param Array of search parameters [ OPTIONAL ]
	 * @return Array of IDs matching search parameters
	 *
	 */
	public static function where($array = array())
	{
		// Get the profiler and memcach instances
		global $__profiler, $__memcache;

		// Tell the profiler we are entering the where section
		$__profiler->enterSection('Model::where');

		// What object are we working with?
		$class_name = get_called_class();


		// :NOTE:
		//		The where cache is object specific and contains an array of search hashes and their results
		// 		This allows for the entire WHERE cache to be deleted if an object that is related is changed
		//	:KLUDGE:
		//		Consider other alternatives for deleting only the where searches a result is in when that
		//		result object is updated, this would let the cached results that do not have the changed
		//		object in them live while only destroying the ones that contain the chancged object in the
		//		results. [ POSSIBLY insead of deleteing the whole cache, simply scan through the cache's array
		//		for the result object and delete elements with this object in it. ? Scalability? ]

		// Key of where cache
		$key = $class_name.':where:array';

		// Get the where cache array
		$where_array = $__memcache->get($key);

		if(NULL == $where_array)
		{
			// If we do not have a cached where array
			// Make an empty array
			$where_array = array();
		}

		// Create a search specific key based on the paramaters
		$search_key = md5(serialize(func_get_args()));

		if ( FALSE != isset($where_array[$search_key]) )
		{
			// If we found the cached result
			$result = $where_array[$search_key];
		}
		else
		{
			// Create a new instance of the class we are working with
			$class = new $class_name;

			// Build the basic select query
			$sql = 'SELECT `id` FROM '.$class->_table.' WHERE TRUE ';

			// Set the where clause to be empty
			$where_clause = '';

			// If there is a limit defined
			if (isset($array['limit']))
				$limit_clause = ' LIMIT '.(int)$array['limit'];
			// If there is an offset defined
			if (isset($array['offset']))
				$offset_clause = ' OFFSET '.(int)$array['offset'];
			// If there is an order defined
			if (isset($array['order']))
				$order_clause = ' ORDER BY '.$array['order']['by'].' '.$array['order']['type'];

			// :NOTE:
			//		Not only are the results cached but also the query and data sets
			//		These will not be deleted when an object is saved, because they
			//		do not store results, only search terms and queries

			// Define the query search key
			$query_key = $class_name.':where:sql-and-data:'.$search_key;

			// Grab the cache result
			$query_result = $__memcache->get($query_key);

			if (NULL != $query_result)
			{
				// If the query/data set was cached

				// Set the SQL
				$sql = $query_result['sql'];

				// Set the data array
				$bind_array = $query_result['data'];
			}
			else
			{
				// If we have to build the query

				// Array for holding bindings of data
				$bind_array = array();

				foreach($class->_columns as $col)
				{
					// Foreach column we can search in this object

					if (FALSE != isset($array[$col]))
					{
						// If we have a parameter for this column

						// Are we doing a != search
						$not = (substr($array[$col],0,1)=='!'?'!':'');
						// Remove spaces from the column name
						$safe_column = preg_replace('/\s/','',$col);
						// Remove the ! from the parameter (if it exists)
						$safe_data = ltrim($array[$col], '!');

						// Prepend the where clause with the new statement
						$where_clause .= ' AND '. $col.' '.$not.'= :'.$safe_column;

						// Check for on save method (this way things like passwords wont have to be hashed before searching for them or updating them)

						// set the name of the method we are searching for onSave[coumn name to UpperCaseAbreviations]
						$method = 'onSave'.str_replace(' ', '', ucwords(str_replace('_',' ', strtolower($col))));

						if ( method_exists($class_name, $method) )
						{
							$bind_array[$safe_column] = $class_name::$method($safe_data);
						}
						else
						{
							$bind_array[$safe_column] = $safe_data;
					 	}
					}
				}
				// Build the final SQL (ignore errors if the limit/order/offset clause are not set
				$sql = $sql.$where_clause.@$order_clause.@$limit_clause.@$offset_clause.';';
				// Cache our query and data for future reference
				$__memcache->set($query_key, array('data'=>$bind_array, 'sql'=>$sql), CACHE_OBJ_EXPIRE_LONGER);
			}

			// Prepare the sql
			$select = $class->_db->prepare($sql);
			// Execute the SQL with the data bound to it
			$select->execute($bind_array);
			// Get the results as the id array
			$ids = $select->fetchAll();

			// Zero out the result var as an empty array (ready for the ids
			$result = array();

			if (FALSE != isset($array['limit']) && '1' == $array['limit'])
			{
				// If we have a limit of one, return just that ID
				if ( isset($ids[0]) )
				{
					// Return that ID
					$result = $ids[0]['id'];
				}
				else
				{
					// Object not found, return NULL
					$result = NULL;
				}
			}
			else
			{
				// We have multiple IDs to return
				foreach($ids as $id)
				{
					// Append result array with this ID
					$result[] = $id['id'];
				}
			}

			// Store the result to the where cache array
			$where_array[$search_key] = $result;
			// Save the cache
			$__memcache->set($key, $where_array, CACHE_OBJ_EXPIRE_LONG);
		}

		// Tell profiler we are leaving where
		$__profiler->leaveSection('Model::where');

		// Return the found result array
		return $result;
	}

	/**
	 * all()
	 *
	 * Returns all instances of the object in the database
	 *
	 * @return Array of all Objects
	 */
	public static function all()
	{
		// Get the class name that we are working with
		$class_name = get_called_class();
		// Return the search results
		return $class_name::find('all');
	}

	/**
	 * first()
	 *
	 * Returns the first instance of the object sorted by ID
	 *
	 * @return Object
	 */
	public static function first()
	{
		// Get the class name that we are working with
		$class_name = get_called_class();
		// Return the search results
		return $class_name::find('first');
	}

	/**
	 * last()
	 *
	 * Returns the last instance of the object sorted by ID
	 *
	 * @return Object
	 */
	public static function last()
	{
		// Get the class name that we are working with
		$class_name = get_called_class();
		// Return the search results
		return $class_name::find('last');
	}


	/**
	 *  create($array)
	 *
	 * Creates a new instance to be inserted into the database on save()
	 * Can be passed an array of values to pre-populate the object with
	 *
	 * @param Array of parameters [OPTIONAL]
	 * @return new Instance
	 */
	public static function create($values = array())
	{
		// Get the profiler instrance
		global $__profiler;

		// Tell the profiler we are entering the create section
		$__profiler->enterSection('Model::create');

		// What object are we working with?
		$class_name = get_called_class();

		// Create a new instance of the object
		$obj = new $class_name;

		// Set the new object flag to TRUE
		$obj->_is_new = TRUE;

		foreach($values as $param=>$value)
		{
			// Foreach value we were passed
			if ( in_array($param, $obj->_columns) )
			{
				// If the value is an object property then set it to the passed value
				$obj->$param = $value;
			}
		}

		// Tell the profiler we are leaving the create section
		$__profiler->leaveSection('Model::create');

		// Return the object instance created
		return $obj;
	}

	/**
	 * Default constructor
	 *
	 * Can be passed FALSE to avoid creating a PDO database link
	 */
	public function __construct($database = TRUE)
	{
		// Get the profiler instrance
		global $__profiler;

		// Tell the profiler we are entering the construct section
		$__profiler->enterSection('Model::__construct');

		// What object are we working with? Also store it in the model data
		$this->_class = $class_name = get_called_class();

		// What table should we use
		$this->_table 	= Inflector::tableize(strtolower($this->_class));

		if($database)
		{
			// If we need a database, create a connection to one
			$this->_db = $this->_getDatabase();
		}

		// Set the columns associated with this object
		$this->_setColumnsFromTable();


		// For the associative properties load them into private variables for easier access
		if (property_exists($class_name,'belongs_to'))
			$this->_belongs_to = $class_name::$belongs_to;
		if (property_exists($class_name,'has_many'))
			$this->_has_many = $class_name::$has_many;
		if (property_exists($class_name,'has_one'))
			$this->_has_one = $class_name::$has_one;

		// Tell the profiler we are leaving the __construct method
		$__profiler->leaveSection('Model::__construct');
	}

	/**
	 * _setColumnsFromTable
	 *
	 * Gets the column listing from the table
	 *
	 */
	private function _setColumnsFromTable()
	{
		// Get the profiler and memcache instrances
		global $__profiler, $__memcache;

		// Tell the profier we are entering the _setCoulumnsFromTable section
		$__profiler->enterSection('Model::_setColumnsFromTable');

		if ($this->_table)
		{
			//If we have an accociated table

			// Check the cache for this table's structure
			$result = $__memcache->get($this->_class.'::table');
			if($result != NULL)
			{
				// If we have a listing then save it to the object
				$this->_columns = $result;
			}
			else
			{
				// Query the database for the columns in the table

				$result = $this->_db->query('SHOW COLUMNS FROM `'.$this->_table.'`');

				if ( !is_object($result) )
				{
					/// If we had an error looking up the columns
					fatal_error('Table `'.$this->_table.'` is missing or corrupt');
				}

				// Get the result of the query
				$result = $result->fetchAll();

				foreach($result as $col)
				{
					// Add the column we found in the result to the objects list of columns
					$this->_columns[] = $col['Field'];
				}

				// Cache the resultant list for easier access
				$__memcache->set($this->_class.'::table', $this->_columns, CACHE_OBJ_EXPIRE_LONGER);
			}
		}

		// Tell the profiler we are leaving the _setColumnsFromTable section
		$__profiler->leaveSection('Model::_setColumnsFromTable');
	}

	/**
	 * updateAttributes()
	 *
	 * Updated an array of attributes
	 *
	 */
	public function updateAttributes($values)
	{
		foreach($values as $col=>$val)
		{
			// For values we have been passed
			if (FALSE != in_array($col, $this->_columns))
			{
				// If this column exists in the object, set it
				$this->$col = $val;
			}
		}
	}

	/**
	 * _setInitial()
	 *
	 * Sets the original data to the current data
	 */
	private function _setInitial()
	{
		// Return whether the assignment of orional to new succeeded
		return ($this->_data_orig = $this->_data);
	}

	/**
	 * revert(?)
	 *
	 * Returns the object or data back to it's original state
	 *
	 * @param [Key = FALSE] Key of data to revert otherwise everything
	 * @return the first object in the database
	 */
	public function revert($key = FALSE)
	{
		// Get the profiler instance
		global $__profiler;
		// Inform the profiler it is entering revert
		$__profiler->enterSection('Model::revert');

		if (!$key)
		{
			// If we were not passed a specific key to reset
			// Reset all data to the origional state
			$this->_data = $this->_data_orig;
		}
		else
		{
			// A specific key to reset has been passed
			if (FALSE != in_array($key, $this->_belongs_to))
			{
				// If this is an association

				// Key should be the ID not the objecty itself
				$key = $key.'_id';
			}
			if (FALSE != array_key_exists($key, $this->_data_orig))
			{
				// If this data existed initially
				// Reset the data to the origional value
				$this->_data[$key] = $this->_data_orig[$key];
			}
			else
			{
				// If the key is not part of the origional data
				// Delete the data
				unset($this->_data[$key]);
			}
		}
		// Inform the profier the section is complete
		$__profiler->leaveSection('Model::revert');
	}

	/**
	 * isChanged()
	 *
	 * Returns if the object has changed from the original state
	 *
	 * @return Bool
	 */

	public function isChanged()
	{
		// Is the data not equal to the origional data
		$is_different_data = $this->_data != $this->_data_orig;
		// Return if the data is different (if it is then the object has changed)
		return $is_different_data;
	}

	/**
	 * changed()
	 *
	 * Returns an array of the changed data columns
	 *
	 * @return Array
	 */
	public function changed()
	{
		// Result array
		$result = array();

		foreach($this->_data as $col => $data)
		{
			// For each index in the object's data array
			if ($this->hasChanged($col))
			{
				// If the column has changed then add it to the changed array
				$result[] = $col;
			}
		}
		// Return the array of changed indexes
		return $result;
	}

	/**
	 * hasChanged()
	 *
	 * Returns boolean if the specified data has changed
	 *
	 * @param data_key
	 * @return Bool
	 */
	public function hasChanged($key)
	{

		// Get the profiler instance
		global $__profiler;
		// Inform the profiler that we are entering the hasChanged method
		$__profiler->enterSection('Model::hasChanged');

		if ( FALSE == isset($this->_data[$key]) && FALSE != in_array($key, $this->_belongs_to) )
		{
			// If this is a belongs_to association check the _id property not the object itself
			$key = $key.'_id';

		}

		// :NOTE:
		// 	The object has changed IFF
		//		The data is set in one array and not in the other,
		// 			OR
		//		The data exists in the current data array AND It is not equal to the old value
		$has_changed = 	(
							array_key_exists($key, $this->_data_orig) != array_key_exists($key, $this->_data)
						) || (
							array_key_exists($key, $this->_data)
							&&
							($this->_data[$key] != $this->_data_orig[$key] )
						);
		// Inform the profier the section is complete
		$__profiler->leaveSection('Model::hasChanged');

		// Return whether the data has changed
		return $has_changed;
	}


	/**
	 * isValid()
	 *
	 * Returns TRUE if and only if the object meets all requirements
	 * Sets the error information as well if any is needed
	 *
	 * @return Boolean Is object Valid
	 */
	public function isValid()
	{
		// Get the profiler instance
		global $__profiler;
		// Inform the profiler that we are entering the hasChanged method
		$__profiler->enterSection('Model::isValid');

		// Clear out any pre-existing errors
		$this->_valid_errors = array();

		// Set the mode to raw for true data access
		$this->_setRawMode(TRUE);

		// Check for presence of variables
		if ( FALSE != isset($this::$validates_presence_of) )
		{
			// If the object has defined things to validate presence of
			// Set the variable making what to check for
			$validates = $this::$validates_presence_of;
			foreach ($validates as $validate)
			{
				// For each thing to check the presence of
				if ( in_array($validate, $this->_belongs_to))
				{
					// If we are checking a belongs to reference
					// Validate the presence of an id as opposed to the object itself
					$validate = $validate.'_id';
				}
				if ( NULL == $this->$validate )
				{
					// If we have no value for the thing to be checked
					// Set the error
					$this->_valid_errors[$validate][] = 'Must be set';
				}
			}
		}

		if ( FALSE != isset($this::$validates_length_of) )
		{
			$validates = $this::$validates_length_of;

			foreach ($validates as $what=>$sizes)
			{
				if( FALSE != isset($sizes['min']) )
				{
					if( strlen($this->$what) < (int)$sizes['min'] )
					{
						$this->_valid_errors[$what][] = 'Length must be at least '.$sizes['min'];
					}
				}
				if( FALSE != isset($sizes['max']) )
				{
					if( strlen($this->$what) > (int)$sizes['max'] )
					{
						$this->_valid_errors[$what][] = 'Length exceeds the max of '.$sizes['min'];
					}
				}
				if( FALSE != isset($sizes['equal']) )
				{
					if( strlen($this->$what) != (int)$sizes['equal'] )
					{
						$this->_valid_errors[$what][] = 'Length must be '.$sizes['min'];
					}
				}
			}
		}

		// Check for uniqueness of variables
		if ( FALSE != isset($this::$validates_uniqueness_of) )
		{
			// If the object has defined things to validate uniqueness of
			// Set the variable making what to check for
			$validates = $this::$validates_uniqueness_of;

			// Define the class name
			$class_name = $this->_class;

			foreach ($validates as $validate)
			{
				// Foreach thing to validate
				if ( TRUE == is_array($validate) )
				{
					// If we need to check a set of values are unique
					// Define an array of search terms
					$where_array = array();

					// If any parameter in the array to check has chaanged
					$changed = ( 1 <= count(array_intersect($this->changed(), $validate)) );

					if(FALSE != $changed)
					{
						// Only run this check if one of the values to check has changed
						foreach($validate as $key)
						{
							// Check this only if we have changed the key
							if (in_array($key, $this->_belongs_to))
							{
								// Check the association ID as opposed to the object
								$key = $key.'_id';
							}
							// Add the search term to the array
							$where_array[$key] = $this->$key;
						}
						// Search for objects having the assigned proprerties using where()
						// 	[ we donly need the ids not the objects that would be returned by find() ]
						$search = $class_name::where($where_array);

						if ( 0 != count($search) )
						{
							// If we have found some objects that have these unique properties
							// Create the error key
							$super_key = implode('-',$validate);
							// Set the error
							$this->_valid_errors[$super_key][] = 'Not unique value set';
						}
					}
				}
				else
				{
					// If we are simply checking one value for uniqueness
					if ( FALSE != $this->hasChanged($validate))
					{
						// If the value has changed form the origional
						if (in_array($validate, $this->_belongs_to))
						{
							// Check the association ID as opposed to the object
							$validate = $validate.'_id';
						}

						// Search for an object using where() on the parameter nessisary
						$search = array($validate => ($this->$validate));
						$search = $class_name::where( $search );

						if ( 0 != count($search) )
						{
							// If we have found some object that has these unique properties
							// Set the error
							$this->_valid_errors[$validate][] = 'Is not unique';
						}
					}
				} // End If Array
			} // End Foreach validate
		} // End Validate Unique

		if ( FALSE != isset($this::$validates_pattern_of) )
		{
			// If the object has defined things to validate the pattern of
			// Set the variable making what to check for
			$validates = $this::$validates_pattern_of;
			$class_name = $this->_class;

			foreach ($validates as $value => $pattern)
			{
				// For each paramter to validate
				if ( NULL != $this->$value && 0 == preg_match('%'.preg_replace('[%]', '\$', $pattern).'%', $this->$value) )
				{
					// If we could not find a match in the value
					$this->_valid_errors[$value][] = 'Does not match the specified pattern';
				}
			}
		}


		// The object is valid IFF we have zero errors
		$valid = ( 0 == count($this->_valid_errors) );

		// Reset Raw Mode
		$this->_setRawMode(FALSE);

		// Inform the profiler that the section is complete
		$__profiler->leaveSection('Model::isValid');

		// Return the validity of the object
		return $valid;
	}

	/**
	 * errors()
	 *
	 * Returns the array of errors found in the last validation
	 *
	 * @return Array of errors for object
	 */

	public function errors()
	{
		// Re-run the validity check
		$this->isValid();

		// Return the error array
		return $this->_valid_errors;
	}

	/**
	 * errorsFor()
	 *
	 * Returns the array of errors for the specified value
	 *
	 * @return Array Error Messages
	 */

	public function errorsFor($key)
	{
		// Re-run the validity check
		$this->isValid();

		// Return the errors for the specified parameter
		return $this->_valid_errors[$key];
	}

	/**
	 * save()
	 *
	 * Saved the changed data into the database
	 *
	 * @return Boolean True IFF the database was updated
	 */
	public function save()
	{
		// Get the profiler and memcache instances
		global $__profiler, $__memcache;
		// Inform the profiler we are entering save
		$__profiler->enterSection('Model::save');

		if (FALSE == $this->isValid()){
			// If this is not valid

			// Inform the profiler we are leaving
			$__profiler->leaveSection('Model::save');

			// Return false
			return FALSE;
		}
		if (FALSE == $this->_table){
			// If there is not table

			// Inform the profiler we are leaving
			$__profiler->leaveSection('Model::save');

			// Return false
			return FALSE;
		}
		if (FALSE == $this->isChanged()){
			// If nothing has changed

			// Inform the profiler we are leaving
			$__profiler->leaveSection('Model::save');

			// Return false
			return FALSE;
		}

		// Set Raw Mode on [ for direct data access ]
		$this->_setRawMode(TRUE);

		if (FALSE != $this->_is_new){
			// If this is a new object
			if ( FALSE != in_array('created', $this->_columns) ){
				// If there is a created column
				$query = 'INSERT INTO '.$this->_table.' (`id`,`created`) VALUES (NULL, NOW());';
			}else{
				// If there is no created column
				$query = 'INSERT INTO '.$this->_table.' (`id`) VALUES (NULL);';
			}
			// Execute the insert query
			$this->_db->query($query);
			// Set the ID to the Last Inert ID
			$this->id = $this->_db->lastInsertId();

			// It is no longer a new object
			$this->_is_new = FALSE;
		}

		// Get the list of items to update
		$changed = $this->changed();

		// What class are we dealing with
		$class_name = $this->_class;

		// Set the update statement to empty
		$statement = '';

		// Set the id for the object
		$data_list = array(':id'=>$this->id);


		foreach($changed as $col)
		{
			// For each changed column

			if ($col != 'id')
			{
				// Ignore the ID column
				if(in_array($col, $this->_belongs_to))
				{
					// Update IDs on belongs to assosciations
					$col = $col.'_id';
				}
				$safe_col = preg_replace('%[\'"\s]%', '', $col);
				// If we have a onSave[Value] method call it

				// generate the method name
				$method = 'onSave'.str_replace(' ', '', ucwords(str_replace('_',' ', strtolower($col))));

				if ( method_exists($class_name, $method) )
				{
					// If the onSave method exists preform it
					$data_list[':'.$safe_col] = $class_name::$method($this->$col);
				}
				else
				{
					// The onSave doesnt exist so just copy the data
					$data_list[':'.$safe_col] = $this->$col;
				}

				// append the Update statement
				$statement .= ' `'.$safe_col.'`= :'.$safe_col.',';

			}
		}
		if ( in_array('modified', $this->_columns) ){
			// If we have a modified column
			$query = 'UPDATE '.$this->_table.' SET '.$statement.' `modified`=NOW() WHERE `id`=:id LIMIT 1;';
		} else {
			// No modified coumn
			$query = 'UPDATE '.$this->_table.' SET '.substr($statement,0,-1).' WHERE `id`=:id LIMIT 1;';
		}

		// Prepare and execute the statement
		$update_statement = $this->_db->prepare($query);
		$update_statement->execute($data_list);
		// Cache the object
		$key = $this->_class.':id:'.$this->id;
		$__memcache->set($key, $this, CACHE_OBJ_EXPIRE);

		// Remove the nessiary search caches
		$__memcache->set($this->_class.':find:first', NULL, 0);
		$__memcache->set($this->_class.':find:last', NULL, 0);
		$__memcache->set($this->_class.':find:all', NULL, 0);
		$__memcache->set($this->_class.':where:array', NULL, 0);
		//$__memcache->flush();
		// Reset Raw Mode
		$this->_setRawMode(FALSE);

		// Inform the profiler we are leaving save
		$__profiler->leaveSection('Model::save');

		// Return true
		return TRUE;
	}

	/**
	 * delete()
	 *
	 * Deletes the object from the database
	 *
	 * @return Boolean True IFF the database was updated
	 */
	public function delete(){
		// Get the global instance of the memcache
		global $__memcache;

		if (FALSE != $this->_is_new){
			// We cannot delete a new object
			return FALSE;
		}
		// Set up delete query
		$query = 'DELETE FROM '.$this->_table.' WHERE `id`=:id LIMIT 1;';

		// Prepare and execute delete
		$delete_statement = $this->_db->prepare($query);
		$delete_statement->execute(array(':id'=>$this->id));
		// Was the entry deleted?
		$success = ( $delete_statement->errorCode() == '000000' );

		// Remove the nessiary search caches
		$__memcache->set($this->_class.':find:first', NULL, 0);
		$__memcache->set($this->_class.':find:last', NULL, 0);
		$__memcache->set($this->_class.':find:all', NULL, 0);
		$__memcache->set($this->_class.':where:array', NULL, 0);
		$__memcache->flush();

		// Return success
		return $success;
	}


	/**
	 * __get()
	 *
	 * Overrides default getter to allow for our data to be dynamic
	 * Also grabs items not explicitly joined. (belongs_to, has_one/many)
	 *
	 * @return data
	 */
	public function __get($name){

		// Define the value to return to NULL
		$value_to_return = NULL;

		if ( property_exists($this->_class,$name) )
		{
			// If the property exists directly
			$value_to_return = $this->{$name};

		}
		else if ( isset($this->_data[$name]) )
		{
			// If it is a data element
			$value_to_return = $this->_data[$name];

		}
		else if ( isset($this->_data[strtolower($name)]) )
		{
			// If the name exists in lowercase
			$value_to_return = $this->_data[strtolower($name)];

		}
		else if ( isset($this->_data[strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name))]) )
		{
			// If we searched capital case but it is really underscore seperated
			$value_to_return = $this->_data[strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name))];

		}
		else
		{
			// Check if this is a has_many/has_one/belongs_to property
			if ( in_array($name, $this->_belongs_to) )
			{
				//   If so then load that object/set of objects and set it to to the property

				if(FALSE == isset($this->_data[$name.'_id']))
				{
					// No object is associated yet
					$value_to_return = NULL;

				}

				// check if we have already made the object
				if ( isset($this->_belongs_to_objects[$name]) )
				{
					// If we have found this before, return it
					$value_to_return = $this->_belongs_to_objects[$name];
				}
				else
				{
					// Class we will construct from this call
					$class = $name;

					if ( FALSE != isset($this->_belongs_to[$name]) && FALSE != is_array($this->_belongs_to[$name]) )
					{
						if ( $this->_belongs_to[$name]['class'] )
						{
							$class = $this->_belongs_to[$name]['class'];
						}
					}

					//  Create the object to return from the data stored
					$value_to_return = $this->_belongs_to_objects[$name] = $class::find( $this->_data[$name.'_id'] );
				}

			}
			else
			{
				// Check for has many and has one links
				if ( TRUE == in_array($name, $this->_has_many) || TRUE == array_key_exists($name, $this->_has_many) )
				{
					// The name needs to be singularized then
					$name_singular = Inflector::singularize($name);
					// If this is a has_many assocuation
					// check if we have already made the object
					if ( isset($this->_has_many_objects[$name]) )
					{
						// If we have found this before, return it
						$value_to_return = $this->_has_many_objects[$name];
					}
					else
					{
						// Class to make
						$class = $name_singular;

						$through = FALSE;

						// :TODO: Make the through relationship checker
						if ( FALSE != isset($this->_has_many[$name]) && FALSE != is_array($this->_has_many[$name]) )
						{
							if ( $this->_has_many[$name]['class'] )
							{
								$class = $this->_has_many[$name]['class'];
							}
							if ( $this->_has_many[$name]['through'] )
							{
								$through = $this->_has_many[$name]['through'];
							}
						}

						if ( FALSE != $through )
						{
							$value_to_return = $this->_has_many_objects[$name] = new Model_Through($this, &$this->$through, $class, $name_singular.'_id');
						}
						else
						{
						// END :TODO:
						// BEGIN WORKING CODE

							// make the object list
							// Find the Id list of the asociation
							$id_list = $class::where( array(strtolower($this->_class.'_id')=>$this->id) );

							// Create a new Model_List object containing the associated objects and store it for later reference
							$this->_has_many_objects[$name] = new Model_List($name_singular, $id_list, $class);

							// Return this new object
							$value_to_return = $this->_has_many_objects[$name];
						}
					}

				}
				else if ( in_array($name, $this->_has_one) )
				{

					// If this is a has_one reference
					// check if we have already made the object
					if ( isset($this->_has_one_objects[$name]) )
					{
						// If we have found this before, return it
						$value_to_return = $this->_has_one_objects[$name];
					}
					else
					{
						// Find the object and return it
						$value_to_return = $this->_has_one_objects[$name] = $name::find( array(strtolower($this->_class.'_id')=>$this->id, 'limit'=>1) );
					}
				}
			}

			// Return the found values / object(s)
			return $value_to_return;

		}

		if (FALSE == $this->_raw_mode && FALSE != method_exists($this->_class, 'get'.str_replace(' ', '', ucwords(str_replace('_',' ', $name)))) )
		{
			// If we are not in Raw Data Mode then search for an overriding getter [ defined in the class file ]
			// Nicely define the method to look for
			$method = 'get'.str_replace(' ', '', ucwords(str_replace('_',' ', $name)));
			// What class are we dealing with
			$class = $this->_class;
			// Set the value to return to the processed value we had before
			$value_to_return = $class::$method($value_to_return);
		}

		return $value_to_return;

	}

	/**
	 * __set()
	 *
	 * Overrides default setter to allow for our data to be dynamic
	 * Also updated the links on items not explicitly joined. (belongs_to, has_one/many)
	 *
	 * @return TRUE
	 */
	public function __set($name, $val)
	{

		if (strtolower($name) == 'id' && $this->_raw_mode == FALSE)
		{
			// Do not set the ID of an object. That is impropper use
			return FALSE;
		}

		if ( in_array($name, $this->_belongs_to) )
		{
			// If this is an association update the link
			$this->_data[$name.'_id'] = ($val->id);
			return TRUE;
		}

		if ( $this->_raw_mode == FALSE && method_exists($this->_class, 'set'.str_replace(' ', '', ucwords(str_replace('_',' ', $name)))) )
		{
			// If we are in Raw Mode and there exists an overriding setter
			// Pass the new value through it's setter (if defined) before we actually set the new value

			// Create the method name
			$method = 'set'.str_replace(' ', '', ucwords(str_replace('_',' ', $name)));
			$class = $this->_class;
			// Change the value to the result of the setter method
			$val = $class::$method($val);
		}

		// Set the value
		if ( property_exists($this->_class,$name) )
		{
			// Sotre the data to the object property
			$this->{$name}=$val;

		}
		else if ( isset($this->_data[$name]) )
		{
			// Sotre the data in the passed index
			$this->_data[$name] = $val;

		}
		else if ( isset($this->_data[strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name))]) )
		{
			// Sotre the data in the underscore seperated index
			$this->_data[strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name))] = $val;

		}
		else
		{
			// Set the lowercase of the name to the value
			$this->_data[strtolower($name)] = $val;
		}

		return TRUE;
	}

	/* Abstract methods for Serializable
	 *   Removes PDO instance from object serialize call.
	 *   Allowing the object to be stored and cached
	 */
	public function serialize(){
		// Return the serialized array of properties defined
		return serialize(array($this->_class, $this->_is_new, $this->_data, $this->_data_orig, $this->_columns, $this->_table, $this->_belongs_to, $this->_has_many, $this->_has_one, $this->_raw_mode));

	}

	/* Abstract methods for Serializable
	 *   Restores all object data from serialized string
	 */
	public function unserialize($serialized){
		// Expand the serialized array to the list of properties
		list($this->_class, $this->_is_new, $this->_data, $this->_data_orig, $this->_columns, $this->_table, $this->_belongs_to, $this->_has_many, $this->_has_one, $this->_raw_mode) = unserialize($serialized);
		// Restore the database property with new connection
		$this->_db = self::_getDatabase();

	}

}