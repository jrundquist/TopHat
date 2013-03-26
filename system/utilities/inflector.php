<?php

/*
pluralize	Apple, Orange, Person, Man	Apples, Oranges, People, Men
singularize	Apples, Oranges, People, Men	Apple, Orange, Person, Man
camelize	Apple_pie, some_thing, people_person	ApplePie, SomeThing, PeoplePerson
underscore	It should be noted that underscore will only convert camelCase formatted words. Words that contains spaces will be lower-cased, but will not contain an underscore.
applePie, someThing	apple_pie, some_thing
humanize	apple_pie, some_thing, people_person	Apple Pie, Some Thing, People Person
tableize	Apple, UserProfileSetting, Person	apples, user_profile_settings, people
classify	apples, user_profile_settings, people	Apple, UserProfileSetting, Person
variable	apples, user_result, people_people	apples, userResult, peoplePeople
slug
*/
class Inflector 
{
	
									//	Array of pluralization rules
	private static $plural = array(
        '/(quiz)$/i'               => '$1zes',
        '/^(ox)$/i'                => '$1en',
        '/([m|l])ouse$/i'          => '$1ice',
        '/(matr|vert|ind)ix|ex$/i' => '$1ices',
        '/(x|ch|ss|sh)$/i'         => '$1es',
        '/([^aeiouy]|qu)y$/i'      => '$1ies',
        '/(hive)$/i'               => '$1s',
        '/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
        '/(shea|lea|loa|thie)f$/i' => '$1ves',
        '/sis$/i'                  => 'ses',
        '/([ti])um$/i'             => '$1a',
        '/(tomat|potat|ech|her|vet)o$/i'=> '$1oes',
        '/(bu)s$/i'                => '$1ses',
        '/(alias)$/i'              => '$1es',
        '/(octop)us$/i'            => '$1i',
        '/(ax|test)is$/i'          => '$1es',
        '/(us)$/i'                 => '$1es',
        '/s$/i'                    => 's',
        '/$/'                      => "s"
    );
									//	Array used to un-pluralize words
    private static $singular = array(
        '/(quiz)zes$/i'             => '$1',
        '/(matr)ices$/i'            => '$1ix',
        '/(vert|ind)ices$/i'        => '$1ex',
        '/^(ox)en$/i'               => '$1',
        '/(alias)es$/i'             => '$1',
        '/(octop|vir)i$/i'          => '$1us',
        '/(cris|ax|test)es$/i'      => '$1is',
        '/(shoe)s$/i'               => '$1',
        '/(o)es$/i'                 => '$1',
        '/(bus)es$/i'               => '$1',
        '/([m|l])ice$/i'            => '$1ouse',
        '/(x|ch|ss|sh)es$/i'        => '$1',
        '/(m)ovies$/i'              => '$1ovie',
        '/(s)eries$/i'              => '$1eries',
        '/([^aeiouy]|qu)ies$/i'     => '$1y',
        '/([lr])ves$/i'             => '$1f',
        '/(tive)s$/i'               => '$1',
        '/(hive)s$/i'               => '$1',
        '/(li|wi|kni)ves$/i'        => '$1fe',
        '/(shea|loa|lea|thie)ves$/i'=> '$1f',
        '/(^analy)ses$/i'           => '$1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => '$1$2sis',
        '/([ti])a$/i'               => '$1um',
        '/(n)ews$/i'                => '$1ews',
        '/(h|bl)ouses$/i'           => '$1ouse',
        '/(corpse)s$/i'             => '$1',
        '/(us)es$/i'                => '$1',
        '/s$/i'                     => ""
    );
									//	Array of words that do not meet regular plural words
    private static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people'
    );
									//	Words that are not countable. 
    private static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );


	/**
	*
	* pluralize()
	*
	* Returns the plural version of the string passed 
	* 
	* @param String singular version
	* @return String plural version
	*
	**/ 
	
	public static function pluralize( $string )
	{
	    // save some time in the case that singular and plural are the same
	    if ( in_array( strtolower( $string ), self::$uncountable ) )
	        return $string;

	    // check for irregular singular forms
	    foreach ( self::$irregular as $pattern => $result )
	    {
	        $pattern = '/' . $pattern . '$/i';

	        if ( preg_match( $pattern, $string ) )
	            return preg_replace( $pattern, $result, $string);
	    }

	    // check for matches using regular expressions
	    foreach ( self::$plural as $pattern => $result )
	    {
	        if ( preg_match( $pattern, $string ) )
	            return preg_replace( $pattern, $result, $string );
	    }

	    return $string;
	}
	
	/**
	 * singularize($word)
	 *
	 *  Returns the singular version of the argument
	 * 
	 * @param string Word to singularize
	 * @return string Singular version of word parameter
	 */
    public static function singularize( $string )
    {
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular plural forms
        foreach ( self::$irregular as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$singular as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

	/**
	 * pluralize_if($count, $word)
	 *
	 *  Returns the plural version of the word if needed.
	 *
	 * @param int the number there are
	 * @param string Word to pluralize
	 * @return string Plural version of word parameter
	 */
    public static function pluralize_if($count, $string)
    {
        if ($count == 1)
            return $string;
        else
            return self::pluralize($string);
    }

	public static function camelize( $string )
	{
		$string_human = Inflector::humanize( $string );
		$string_camel = preg_replace( '/([\s+])/', '', $string_human );
		return $string_camel;
	}
	
	public static function underscore( $string )
	{
		$string_under = preg_replace( '/(?<=[a-z])([A-Z])/', '_$1', $string) ;
		$string_lower = strtolower( $string_under );
		return $string_lower;
	}
	
	public static function humanize( $string )
	{
		$string_lower = strtolower( $string );
		$string_split = preg_replace('/([_\s]+)/', ' ', $string_lower);
		$string_ucase = ucwords( $string_split );
		return $string_ucase;
	}
	
	public static function tableize( $string )
	{
		$string_plural 	= Inflector::pluralize( $string );
		$string_table 	= Inflector::underscore( $string_plural );
		return $string_table;
	}
	
	
	public static function classify( $string )
	{
		$string_single 	= Inflector::singularize( $string );
		$string_camel	= Inflector::camelize( $string_single );
		return $string_camel;
	}
}