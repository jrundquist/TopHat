<?php
require_once('../bootstrap.php');
require_once('../system/model.php');
require_once('../system/model_list.php');
require_once('../system/model_through.php');
require_once('../models/book.php');
require_once('../models/author.php');
require_once('../models/genre.php');

global $__memcache;


class ModelTest extends PHPUnit_Framework_TestCase
{
	
    protected function setUp()
    {
 		global $__memcache;

		if (FALSE == is_object($__memcache))
		{
			$__memcache = new Memcached();
			$__memcache->addServer( 'localhost', 11211 );
		}
    }
 
    protected function tearDown()
    {
        // VOID
    }

	public function testFindFirst()
    {
        $book = Book::first();
		$this->assertEquals(TRUE, is_object($book));
		$this->assertEquals('Book', get_class($book));
		unset($book);
    }

	public function testFindLast()
    {
        $book = Book::last();
		$this->assertEquals(TRUE, is_object($book));
		$this->assertEquals('Book', get_class($book));
		unset($book);
    }

	public function testFindAll()
    {
        $books = Book::all();
        $this->assertEquals(TRUE, is_array($books));
		foreach($books as $book){
			$this->assertEquals(TRUE, is_object($book));
			$this->assertEquals('Book', get_class($book));
		}
		unset($book);
    }

	public function testFindById(){
		$book = Book::find(1);
		$this->assertEquals(1, $book->id);
		unset($book);
	}
	
	public function testCreate(){
		$book = Book::create();
		$this->assertEquals(TRUE, $book->_is_new);
		$this->assertEquals(NULL, $book->id);
		$this->assertEquals(TRUE, in_array('id', $book->_columns));
		unset($book);
		
		$book = Book::create(array('title'=>'This is a new book!', 'upc'=>'8DFF6A8D'));
		$this->assertEquals(TRUE, $book->_is_new);
		$this->assertEquals('This is a new book!', $book->title);
		$this->assertEquals('8DFF6A8D', $book->upc);
		unset($book);
	}
	
	public function testBelongsTo(){
		$book = Book::find(1);
		$this->assertEquals('Author', get_class($book->author));
		$this->assertEquals('Genre' , get_class($book->genre ));
		unset($book);
	}
	
	public function testHasMany(){
		$author = Author::first();
		$this->assertEquals('Model_List', get_class($author->books));
		$this->assertEquals('Book', get_class($author->books->first()));
		$this->assertEquals(TRUE, $author->books->hasNext());
		$this->assertEquals('Book', get_class($author->books->next()));
		unset($author);
	}
	
	public function testChanged(){
		$book = Book::find(1);
		
		$this->assertEquals(FALSE, $book->isChanged());
		
		$book->author_id++;
		$book->title = $book->title.' NEW ';
		
		$this->assertEquals(TRUE, $book->isChanged());
		$this->assertEquals(TRUE, $book->hasChanged('title'));
		$this->assertEquals(array('title', 'author_id'), $book->changed());
		unset($book);
	}
	
	public function testSaveDelele(){
		$book = Book::create();
		$this->assertEquals(TRUE, $book->_is_new);
		$book->title = 'This is a new book for the test';
		$book->upc = '00000000';
		$book->author = Author::first();
		$this->assertEquals(TRUE, $book->save());
		
		$this->assertEquals(FALSE, $book->_is_new);

		$book_2 = Book::create();
		$this->assertEquals(TRUE, $book_2->_is_new);
		$book_2->title = 'This is a new book for the test';
		$book_2->author = Author::first();
		$book_2->upc = '00000001';
		
		$this->assertEquals(FALSE, $book_2->isValid());
		$this->assertEquals(FALSE, $book_2->save());
		
		$this->assertEquals(array('Not unique value set'), $book_2->errorsFor('title-author'));
		
		$this->assertEquals(TRUE, $book->delete());
	}
	
	public function testSetGet(){
		
		$book = Book::create();
		$book->upc = "james";
		$book->_setRawMode(TRUE);
		$this->assertEquals('ae', $book->upc);
		$book->_setRawMode(FALSE);
		$this->assertEquals('AE', $book->upc);
		$this->assertEquals(array('Does not match the specified pattern'), $book->errorsFor('upc'));
	
	}
}