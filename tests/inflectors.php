<?php
require_once('../bootstrap.php');


class InflectorsTest extends PHPUnit_Framework_TestCase
{

	public function testPluralize()
    {
		$this->assertEquals( 'cats' , 		Inflector::pluralize('cat') );
		$this->assertEquals( 'quizzes' , 	Inflector::pluralize('quiz') );
		$this->assertEquals( 'statuses' , 	Inflector::pluralize('status') );
		$this->assertEquals( 'authors' ,	Inflector::pluralize('author') );
		$this->assertEquals( 'big dogs' , 	Inflector::pluralize('big dog') );
    }

	public function testSingularize()
    {
		$this->assertEquals( 'cat' , 	Inflector::singularize('cats') );
		$this->assertEquals( 'quiz' , 	Inflector::singularize('quizzes') );
		$this->assertEquals( 'status' , Inflector::singularize('statuses') );
		$this->assertEquals( 'author' ,	Inflector::singularize('authors') );
		$this->assertEquals( 'user' , 	Inflector::singularize('users') );
    }

	public function testPluralizeIf()
    {
		$this->assertEquals( 'cat' , 		Inflector::pluralize_if(1, 'cat') );
		$this->assertEquals( 'cats', 		Inflector::pluralize_if(9, 'cat') );
		$this->assertEquals( 'minute' , 	Inflector::pluralize_if(1, 'minute') );
		$this->assertEquals( 'minutes' , 	Inflector::pluralize_if(5, 'minute') );
		$this->assertEquals( 'people' , 	Inflector::pluralize_if(3, 'person') );
    }

	public function testCamelize()
    {
		$this->assertEquals( 'CatDog' , 				Inflector::camelize('cat_dog') );
		$this->assertEquals( 'ApplePie', 				Inflector::camelize('APPLE PIE') );
		$this->assertEquals( 'Terminal' , 				Inflector::camelize('terminal') );
		$this->assertEquals( 'ThisIsGood' , 			Inflector::camelize('This iS good') );
		$this->assertEquals( 'MixedInputTypeString' , 	Inflector::camelize('Mixed_ Input__type string') );
    }

	public function testUnderscore()
    {
		$this->assertEquals( 'cat_dog' , 		Inflector::underscore('cat_dog') );
		$this->assertEquals( 'apple_pie', 		Inflector::underscore('ApplePie') );
		$this->assertEquals( 'terminal' , 		Inflector::underscore('terminal') );
		$this->assertEquals( 'this_is_good' , 	Inflector::underscore('ThisIsGood') );
		$this->assertEquals( 'camel_case' , 	Inflector::underscore('CamelCase') );
    }


	public function testHumanize()
    {
		$this->assertEquals( 'Apple Pie' , 					Inflector::humanize('Apple Pie') );
		$this->assertEquals( 'Top Hat', 					Inflector::humanize('top_hat') );
		$this->assertEquals( 'Terminal' , 					Inflector::humanize('terminal') );
		$this->assertEquals( 'Mixed Input Type String' , 	Inflector::humanize('Mixed_ Input__type string') );
    }

	public function testTableize()
    {
		$this->assertEquals( 'apples' , 				Inflector::tableize('apple') );
		$this->assertEquals( 'top_hats' , 				Inflector::tableize('top_hat') );
		$this->assertEquals( 'user_profile_settings', 	Inflector::tableize('UserProfileSetting') );
    }


	public function testClassify()
    {
		$this->assertEquals( 'Book' , 					Inflector::classify('books') );
		$this->assertEquals( 'TopHat' , 				Inflector::classify('top_hats') );
		$this->assertEquals( 'UserProfileSetting', 		Inflector::classify('user_profile_settings') );
    }
}