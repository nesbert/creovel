<?php
/*

Class: inflector
	Inflector for pluralize and singularize English nouns.
	This Inflector is a port of Ruby on Rails Inflector.
	It can be really helpful for developers that want to create frameworks based on naming conventions rather than configurations.
	It was ported to PHP for the Akelos Framework, a multilingual Ruby on Rails like framework for PHP that will be launched soon.

Credits:
	Akelos PHP Application Framework
	Copyright (c) 2002-2006, Akelos Media, S.L.  http://www.akelos.com/

*/

class Inflector
{
	// Section: Public

    /*

	Function: pluralize
		Pluralizes English nouns.

	Parameters:	
		word - English noun to pluralize

	Returns:	
		Plural noun
	
	*/

    public function pluralize($word)
    {
		$plural = array
		(
			'/(matr)ix$/i' => '\1ices',
			'/(octop|vir)us$/i' => '\1i',
			'/([m|l])ouse/i' => '\1ice',
			'/(tomato)$/i' => '\1es',
			'/(th)$/i' => '\1s',
			'/(h)$/i' => '\1es',
			'/(ay)$/i' => '\1s',
			'/y$/i' => '\1ies',
			'/^(ox)/i' => '\1en',
			'/(ex)$/i' => 'ices',
			'/(x)$/i' => '\1es',
			'/(ss)$/i' => '\1es',
			'/(us)$/i' => '\1es',
			'/(sis)$/i' => 'ses',
			'/(f|fe)$/i' => 'ves',
			'/(n)ews$/i' => '\1ews',
			'/ium$/i' => '\1ia',
			'/([ti])a$/i' => '\1um',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/(^analy)ses$/i' => '\1sis',
			'/([^f])ves$/i' => '\1fe',
			'/(hive)s$/i' => '\1',
			'/(tive)s$/i' => '\1',
			'/([lr])ves$/i' => '\1f',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/(s)eries$/i' => '\1eries',
			'/(m)ovies$/i' => '\1ovie',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/(bus)es$/i' => '\1',
			'/(lo)$/i' => '\1es',
			'/(o)es$/i' => '\1',
			'/(shoe)s$/i' => '\1',
			'/(ax)is$/i' => '\1es',
			'/(us)i$/i' => '\1us',
			'/(vert|ind)ices$/i' => '\1ex',
			'/(alias|status)$/i' => '\1es',
			'/(iz)$/i' => '\1zes',
			'/(tis)$/i' => 'tes',
			'/s$/i' => 's',
			'/$/' => 's'
		);

        $uncountable = array( 'data', 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' );

		$irregular = array
		(
			'person' => 'people',
			'man' => 'men',
			'child' => 'children',
			'sex' => 'sexes',
			'move' => 'moves'
		);

        $lowercased_word = strtolower($word);

        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_plural.')$/i', $word, $arr)) {
                return preg_replace('/('.$_plural.')$/i', substr($arr[0],0,1).substr($_singular,1), $word);
            }
        }

        foreach ($plural as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }
        return false;

    }

	/*

	Function: singularize
    	Singularizes English nouns.

	Parameters:	
    	word - English noun to singularize
		
	Return:	
		Singular noun.

	*/

    public function singularize($word)
    {
		$singular = array
		(
			'/(n)ews$/i' => '\1ews',
			'/([ti])a$/i' => '\1um',
			'/(perspective)a$/i' => '\1um',
			'/(analy|ba|diagno|parenthe|progno|synop|the)ses$/i' => '\1\2sis',
			'/(^analy)ses$/i' => '\1sis',
			'/(archive)s$/i' => '\1',
			'/(hal)ves$/i' => '\1f',
			'/(dwar)ves$/i' => '\1f',
			'/(tive)s$/i' => '\1',
			'/(l)ves/i' => '\1f',
			'/ves$/i' => '\1fe',
			'/(ax)es/i' => '\1is',
			'/([^f])ves$/i' => '\1fe',
			'/(hive)s$/i' => '\1',
			'/(tive)s$/i' => '\1',
			'/([lr])ves$/i' => '\1f',
			'/(movie)s$/i' => '\1',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/(s)eries$/i' => '\1eries',
			'/(m)ovies$/i' => '\1ovie',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/([m|l])ice$/i' => '\1ouse',
			'/(bus)es$/i' => '\1',
			'/(shoe)s$/i' => '\1',
			'/(o)es$/i' => '\1',
			'/(cris|ax|test)es$/i' => '\1is',
			'/(octop|vir)i$/i' => '\1us',
			'/(alias|status)es$/i' => '\1',
			'/^(ox)en/i' => '\1',
			'/(vert|ind)ices$/i' => '\1ex',
			'/(matr)ices$/i' => '\1ix',
			'/(quiz)zes$/i' => '\1',
			'/s$/i' => '',
        );

        $uncountable = array( 'data', 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' );

        $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

        $lowercased_word = strtolower($word);
        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

	/*
	
	Function: titleize
    	Converts an underscored or CamelCase word into a English sentence.
    	The titleize public function converts text like "WelcomePage", "welcome_page" or  "welcome page" to this "Welcome Page".
    	If second parameter is set to 'first' it will only capitalize the first character of the title.

	Parameters:	
		word - Word to format as tile
    	uppercase - If set to 'first' it will only uppercase the first character. Otherwise it will uppercase all the words in the title.

	Return:	
		Text formatted as title

	*/

    public function titleize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'first' ? 'ucfirst' : 'ucwords';
        return $uppercase(self::humanize(self::underscore($word)));
    }

    /*

	Function: camelize			
		Returns given word as CamelCased
    	Converts a word like "send_email" to "SendEmail". It will remove non alphanumeric character from the word, so "who's online" will be converted to "WhoSOnline"
	
	Parameters:
		word - Word to convert to camel case

	Return:
		UpperCamelCasedWord

	*/

    public function camelize($word, $lowercamel = false)
    {
		$word = str_replace(' ','',ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',$word)));
		if ($lowercamel) $word = strtolower(substr($word, 0, 1)) . substr($word, 1);

		return $word;
    }

    /*

	Function: underscore	
		Converts a word "into_it_s_underscored_version"
    	Convert any "CamelCased" or "ordinary Word" into an "underscored_word".
    	This can be really useful for creating friendly URLs.

	Parameters:	
    	word - Word to underscore

	Returns:	
		Underscored word
	 
	*/

    public function underscore($word)
    {
        return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/','_', preg_replace('/([a-z\d])([A-Z])/','\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/','\1_\2',$word))));
    }

    /*

	Function:	
		Returns a human-readable string from $word
    	Returns a human-readable string from $word, by replacing underscores with a space, and by upper-casing the initial character by default.
    	If you need to uppercase all the words you just have to pass 'all' as a second parameter.

	Parameters:	
    	word - String to "humanize"
		uppercase - If set to 'all' it will uppercase all the words instead of just the first one.
	
	Returns:
    	Human-readable word
	 
	*/

    public function humanize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
        return $uppercase(str_replace('_',' ',preg_replace('/_id$/', '',$word)));
    }

    /*

	Function: variablize	
		Same as camelize but first char is underscored
    	Converts a word like "send_email" to "sendEmail". It will remove non alphanumeric character from the word, so "who's online" will be converted to "whoSOnline"
   		See <camelize>

	Parameters:
		word - Word to lowerCamelCase

	Returns:
    	Returns a lowerCamelCasedWord
	 
	*/

    public function variablize($word)
    {
        $word = self::camelize($word);
        return strtolower($word[0]).substr($word,1);
    }

    /*

	Function: tabelize	
		Converts a class name to its table name according to rails naming conventions.
    	Converts "Person" to "people"
    	See <classify>

	Parameters:	
		class_name - Class name for getting related table_name.
		
	Returns:	
		plural_table_name

	*/

    public function tableize($class_name)
    {
        return self::pluralize(self::underscore($class_name));
    }

    /*
			
	Function:	
		Converts a table name to its class name according to rails naming conventions.
    	Converts "people" to "Person"
		See <tableize>

	Parameters:
    	table_name - Table name for getting related ClassName.
		
	Returns:	
		SingularClassName

	*/

    public function classify($table_name)
    {
        return self::camelize(self::singularize($table_name));
    }

    /*

	Function: ordanlize	
		Converts number to its ordinal English form.
    	This method converts 13 to 13th, 2 to 2nd ...

	Parameters:	
		number - Number to get its ordinal value

	Returns:
		Ordinal representation of given string.

	*/

    public function ordinalize($number)
    {
        if (in_array(($number % 100),range(11,13))){
            return $number.'th';
        }else{
            switch (($number % 10)) {
                case 1:
                return $number.'st';
                break;
                case 2:
                return $number.'nd';
                break;
                case 3:
                return $number.'rd';
                default:
                return $number.'th';
                break;
            }
        }
    }
}