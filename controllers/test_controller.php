<?php

class test_controller extends controller{
	public function index(){
		for($i=1;$i<=7;$i++){
			echo "<hr />";
			Router::factoryRequest('/test/test'.$i);
		}
	}
	public function info(){
		phpinfo();
	}
	public function test1(){
		echo "<h1>Testing model objects that just extend Model</h1><h2>Book and Author</h2>";
		$genre = Genre::create();
		$this->echoQuery('$genre = Genre::create();');
		$this->echoResult($genre);
		
		$this->echoQuery('$genre->name = \'Horror\'');
		$genre->name = 'Horror';
		$this->echoResult($genre->name);
		
		$this->echoQuery('$genre->save()');
		$save_result = $genre->save();
		$this->echoResult($save_result);
		if ( $save_result == FALSE ){
			$this->echoQuery('$genre->errors()');
			$this->echoResult($genre->errors());
		}
	}
	
	public function test2(){
		$this->echoQuery('$book = Book::find(1);');
		
		$book = Book::find(1);
		
		$this->echoResult($book);
		$this->echoQuery('$book->title;');
		$this->echoResult($book->title);
		$this->echoQuery('$book->author_id;');
		$this->echoResult($book->author_id);
		$this->echoQuery('$book->author;');
		$this->echoResult($book->author);
		$this->echoQuery('$book->title = \'Cat in the Hat\';');
		$book->title = 'Cat in the Hat';
		$this->echoResult($book->title);
		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		$this->echoQuery('$book->hasChanged(\'title\');');
		$this->echoResult($book->hasChanged('title'));
		$this->echoQuery('$book->hasChanged(\'author\');');
		$this->echoResult($book->hasChanged('author'));

		$author = Author::last();
		$this->echoQuery('$author = Author::last();');
		$this->echoResult($author);

		$this->echoQuery('$book->author = $author;');
		$this->echoResult($book->author = $author);

		$this->echoQuery('$book');
		$this->echoResult($book);

		$this->echoQuery('$book->author_id;');
		$this->echoResult($book->author_id);

		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		$this->echoQuery('$book->hasChanged(\'title\');');
		$this->echoResult($book->hasChanged('title'));
		$this->echoQuery('$book->hasChanged(\'author\');');
		$this->echoResult($book->hasChanged('author'));

		$this->echoQuery('$book->summary = "This is a good book that covers many things";');
		$book->summary = "This is a good book that covers many things";
		$this->echoResult($book->summary);

		$book->revert('author');
		$this->echoQuery('$book->revert("author");');
		$this->echoResult($book->revert('author'));
		$this->echoQuery('$book->author');
		$this->echoResult($book->author);
		$this->echoQuery('$book->hasChanged(\'author\');');
		$this->echoResult($book->hasChanged('author'));

		$this->echoQuery('$book->revert();');
		$this->echoResult($book->revert());

		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		$this->echoQuery('$book->hasChanged(\'title\');');
		$this->echoResult($book->hasChanged('title'));
		$this->echoQuery('$book->hasChanged(\'author\');');
		$this->echoResult($book->hasChanged('author'));
		$this->echoQuery('$book->summary');
		$this->echoResult($book->summary);
		$this->echoQuery('$book->hasChanged(\'summary\');');
		$this->echoResult($book->hasChanged('summary'));

		$this->echoQuery('$book->save()');
		$this->echoResult($book->save());		
	}
	
	public function test3(){		
		$book = Book::find(1);
		$this->echoQuery('$book = Book::find(1);');
		$this->echoResult($book);
		$this->echoQuery('$book->title;');
		$this->echoResult($book->title);
		if($book->title == 'Cat in the Hat'){
			$this->echoQuery('$book->title = \'Hop on Pop\';');
			$book->title = 'Hop on Pop';
		}else{
			$this->echoQuery('$book->title = \'Cat in the Hat\';');
			$book->title = 'Cat in the Hat';
		}
		$this->echoResult($book->title);
		
		$this->echoQuery('$book->genre = Genre::find(1);');
		$book->genre = Genre::find(1);
		$this->echoResult($book->genre);
		
		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		$this->echoQuery('$book->hasChanged(\'title\');');
		$this->echoResult($book->hasChanged('title'));
		$this->echoQuery('$book->save();');
		$this->echoResult($book->save());		
	}
	
	public function test4(){
		$book = Book::find(1);
		$this->echoQuery('$book = Book::find(1);');
		$this->echoResult($book);
		$this->echoQuery('$book->title;');
		$this->echoResult($book->title);
		if($book->author_id == '1'){
			$this->echoQuery('$book->author = Author::find(2);');
			$book->author = Author::find(2);
		}else{
			$this->echoQuery('$book->author = Author::find(1);');
			$book->author = Author::find(1);
		}
		$this->echoResult($book->author);
		$this->echoQuery('$book->author_id');
		$this->echoResult($book->author_id);
		
		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		$this->echoQuery('$book->hasChanged(\'author\');');
		$this->echoResult($book->hasChanged('author'));
		$this->echoQuery('$book->save();');
		$this->echoResult($book->save());
	}
	public function test5(){
		$books = Book::find(array('author_id'=>'1', 'title'=>'Go Dog Go'));
		$this->echoQuery('$books =Book::find(array("author_id"=>"1", "title"=>"Go Dog Go"));');
		$this->echoResult($books);
	}
	public function test6(){
		$author = Author::first();
		$this->echoQuery('$author = Author::first();');
		$this->echoResult($author);
		$this->echoQuery('$author->books->ids()');
		$this->echoResult($author->books->ids());
		$this->echoQuery('$author->books->all()');
		$this->echoResult($author->books->all());
		$this->echoQuery('$author->books->hasNext()');
		$this->echoResult($author->books->hasNext());
		$this->echoQuery('$author->books->next()->id');
		$this->echoResult($author->books->next()->id);
		$this->echoQuery('$author->books->hasNext()');
		$this->echoResult($author->books->hasNext());
		$this->echoQuery('$author->books->next()->id');
		$this->echoResult($author->books->next()->id);
		$this->echoQuery('$author->books->hasPrevious()');
		$this->echoResult($author->books->hasPrevious());
		$this->echoQuery('$author->books->previous()->id');
		$this->echoResult($author->books->previous()->id);
		$this->echoQuery('$author->books->next()->id');
		$this->echoResult($author->books->next()->id);
		$this->echoQuery('$author->books->add(4)');
		$this->echoResult($author->books->add(4));
		$this->echoQuery('$author->books->ids()');
		$this->echoResult($author->books->ids());
		$this->echoQuery('$author->books->hasNext()');
		$this->echoResult($author->books->hasNext());
		$this->echoQuery('$author->books->hasPrevious()');
		$this->echoResult($author->books->hasPrevious());
		$this->echoQuery('$author->books->previous()->id');
		$this->echoResult($author->books->previous()->id);
		$this->echoQuery('$author->books->ids()');
		$this->echoResult($author->books->ids());
		$this->echoQuery('$author->books->remove(1)');
		$this->echoResult($author->books->remove(1));
		$this->echoQuery('$author->books->ids()');
		$this->echoResult($author->books->ids());
	}
	
	public function test7(){
		$book = Book::find(1);
		$this->echoQuery('$book = Book::find(1);');
		$this->echoResult($book);
		
		$this->echoQuery('$book->title = \'Hop on Pop\';');
		$book->title = 'Hop on Pop';
		$this->echoResult($book->title);
		// 
		$this->echoQuery('$book->upc = \'qwedsaws\';');
		$book->upc = 'qwedsaws';
		$this->echoResult($book->upc);

		$this->echoQuery('$book->author_id = 1');
		$book->author_id = 1;
		$this->echoResult($book->author);

		$this->echoQuery('$book->isValid()');
		$this->echoResult($book->isValid());
		
		$this->echoQuery('$book->errors()');
		$this->echoResult($book->errors());
		
		$this->echoQuery('$book->errorsFor(\'upc\');');
		$this->echoResult($book->errorsFor('upc'));
		
		$this->echoQuery('$author = Author::create()');
		$author = Author::create();
		$this->echoResult($author);
		
		$this->echoQuery('$author->errors()');
		$this->echoResult($author->errors());
		
		$this->echoQuery('$author->first_name = \'Theodor\'');
		$author->first_name = 'Theodor';
		$this->echoResult($author->first_name);
		
		$this->echoQuery('$author->middle_name = \'Seuss\'');
		$author->middle_name = 'Seuss';
		$this->echoResult($author->middle_name);
		
		$this->echoQuery('$author->last_name = \'Geisel\'');
		$author->last_name = 'Geisel';
		$this->echoResult($author->last_name);
		
		$this->echoQuery('$author->last_name = \'Dr. Seuss\'');
		$author->pen_name = 'Dr. Seuss';
		$this->echoResult($author->pen_name);
		
		
		$this->echoQuery('$author->errors()');
		$this->echoResult($author->errors());
		
		$this->echoQuery('$author->born = \'Nineteen Hundred and Ninety\'');
		$author->born = 'Nineteen Hundred and Ninety';
		$this->echoResult($author->born);
		
		$this->echoQuery('$author->errors()');
		$this->echoResult($author->errors());
		
		$this->echoQuery('$author->born = \'1990\'');
		$author->born = '1990';
		$this->echoResult($author->born);
		
		$this->echoQuery('$author->errors()');
		$this->echoResult($author->errors());
		
		
		$this->echoQuery('$author->died = \'NA\'');
		$author->died = 'NA';
		$this->echoResult($author->died);
		
		$this->echoQuery('$author->errors()');
		$this->echoResult($author->errors());
		if(FALSE){
		
		}
	}
	
	public function test8(){
		$book = Book::find(1);
		$this->echoQuery('$book = Book::find(1);');
		$this->echoResult($book);
		
		$this->echoQuery('$book->upc');
		$this->echoResult($book->upc);
		
		$this->echoQuery('$book->upc = \'HomeOfTheBrave90\'');
		$book->upc = 'HomeOfTheBrave90';
		$this->echoResult($book->upc);
		
		$this->echoQuery('$book');
		$this->echoResult($book);
		
		$this->echoQuery('$book->save();');
		$this->echoResult($book->save());
		
		
		$this->echoQuery('$book->errors()');
		$this->echoResult($book->errors());
	}
	
	public function test9(){
		$this->echoQuery('$user = User::create(array(\'username\'=>\'testUser\', \'email\'=>\'test@tophat.dev\'));');
		$user = User::create(array('username'=>'testUser', 'email'=>'test@tophat.dev'));
		$this->echoResult($user);
	}
	
	public function memcached(){
		global $__profiler, $__memcache;
		$this->echoQuery('$m = new Memcached();');
		$this->echoResult($__memcache);
		
		$this->echoQuery('$__memcache->addServer(\'localhost\', 11211);');
		$__memcache->addServer('localhost', 11211);
		$this->echoResult($m);
		
		$this->echoQuery('$__memcache->set(\'int\', 99)');
		$this->echoResult($__memcache->set('int', 99));
		
		$this->echoQuery('$__memcache->set(\'string\', \'Some random string\')');
		$this->echoResult($__memcache->set('string', 'Some random string'));
		
		$this->echoQuery('$__memcache->set(\'array\', array(\'11\', 12));');
		$this->echoResult($__memcache->set('array', array('11',12)));
		
		$this->echoQuery('$book = Book::find(1);');
				
		$__profiler->enterSection("build_book");
		$book = Book::find(1);
		$__profiler->leaveSection("build_book");
		
		$this->echoResult($book);
		$this->echoQuery('$__memcache->set(\'first_book\', $book, time()+300);');
		$this->echoResult($__memcache->set('first_book', $book, time()+300));

		$this->echoQuery('$__memcache->get(\'int\');');
		$this->echoResult($__memcache->get('int'));
		
		$this->echoQuery('$__memcache->get(\'string\');');
		$this->echoResult($__memcache->get('string'));
		
		$this->echoQuery('$__memcache->get(\'array\');');
		$this->echoResult($__memcache->get('array'));
		
		$this->echoQuery('$__memcache->get(\'first_book\');');
		$__profiler->enterSection("get_book");
		$book = $__memcache->get('first_book');
		$__profiler->leaveSection("get_book");
		
		$this->echoResult($book);
		
		$__profiler->display();
	}
	public function flush_memcached(){
		
		global $__profiler, $__memcache;
		$this->echoQuery('$__memcache->flush();');
		
		$__profiler->enterSection("Flush");
		$this->echoResult($__memcache->flush());
		$__profiler->leaveSection("Flush");
		
		$__profiler->display();
	}
	
	
	public function create(){
		$book = Book::create();
		$this->echoQuery('$book = Book::create();');
		$this->echoResult($book);
		
		$this->echoQuery('$book->title = \'How the Grinch Stole Christmas!\';');
		$book->title = 'How the Grinch Stole Christmas!';
		$this->echoResult($book->title);
		
		$this->echoQuery('$book->author = Author::find(1);');
		$book->author = Author::find(1);
		$this->echoResult($book->author);
		
		$this->echoQuery('$book->isChanged();');
		$this->echoResult($book->isChanged());
		$this->echoQuery('$book->changed();');
		$this->echoResult($book->changed());
		
		$this->echoQuery('$book->save();');
		$this->echoResult($book->save());
		
		$this->echoQuery('$book->delete();');
		$this->echoResult($book->delete());
		
		
	}
	
	public function user_make(){
		$this->echoQuery('$user = User::create();');
		$user = User::create();
		$this->echoResult($user);
		
		$this->echoQuery('$user->username = \'james\'');
		$user->username = 'james';
		$this->echoResult($user->username);
		
		$this->echoQuery('$user->password = \'$)($%*)(\'');
		$user->password = '$)($%*)(';
		$this->echoResult($user->password);
		
		$this->echoQuery('$user->errors()');
		$this->echoResult($user->errors());
		
		$this->echoQuery('$user->password = \'ru440417\'');
		$user->password = 'ru440417';
		$this->echoResult($user->password);
		
		$this->echoQuery('$user->email = \'jrscienceguy@gmail.com\'');
		$user->email = 'jrscienceguy@gmail.com';
		$this->echoResult($user->email);
		
		$this->echoQuery('$user->save();');
		$this->echoResult($user->save());
		
		$this->echoQuery('$user->errors()');
		$this->echoResult($user->errors());
		
		$this->echoQuery('$user');
		$this->echoResult($user);
		
		$this->echoQuery("\$user2 = User::find(array('username'=>'james', 'password'=>'ru440417'))");
		$user2 = User::find(array('username'=>'james', 'password'=>'ru440417'));
		$this->echoResult($user2);
	}
	
	public function user_test(){
		$user = User::find(array('username'=>'james', 'password'=>'pass', 'limit'=>1));
		$this->echoQuery('$user->friendships->count()');
		$this->echoResult($user->friendships->count());
		// var_dump($user->friendships);
		// $tmp=$user->friendships->first();
		// 
		// $frnd = User::find($tmp->friend_id);
		// $this->echoResult($frnd);
		
		$this->echoQuery('$user->friends->all()');
		$this->echoResult($user->friends->all());
		
		$this->echoQuery('$user->profile->gender');
		$this->echoResult($user->profile->gender);
		
		$this->echoQuery('$user->profile->birthday');
		$this->echoResult($user->profile->birthday);
		
	}
	private function echoQuery($query){
		echo '<span style="font-weight:bold; color:#009">topHatTesting:'.date('H:i').'>></span> '.$query."<br />\n";
	}

	private function echoResult($thing){
		echo '<span style="font-weight:bold; color:#090">>>></span> ';var_dump($thing);echo "<br /><br />\n";
	}
	
}
