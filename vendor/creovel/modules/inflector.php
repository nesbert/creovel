<?php
/**
 * Inflector for pluralize and singularize English nouns. This Inflector is a
 * port of Ruby on Rails Inflector. It can be really helpful for developers
 * that want to create frameworks based on naming conventions rather than
 * configurations.
 *
 * It was ported to PHP for the Akelos Framework, a multilingual Ruby on Rails
 * like framework for PHP that will be launched soon.
 * 
 * Akelos PHP Application Framework
 * Copyright (c) 2002-2006, Akelos Media, S.L. http://www.akelos.com/
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/
class Inflector extends ModuleBase
{
    /**
     * Plural rules array.
     *
     * @var array
     **/
    public static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])um$/i'             => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias)$/i'              => "$1es",
        '/(octop)us$/i'            => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );
    
    /**
     * Singular rules array.
     *
     * @var array
     **/
    public static $singular = array(
            '/(quiz)zes$/i'             => "$1",
            '/(matr)ices$/i'            => "$1ix",
            '/(vert|ind)ices$/i'        => "$1ex",
            '/^(ox)en$/i'               => "$1",
            '/(alias)es$/i'             => "$1",
            '/(octop|vir)i$/i'          => "$1us",
            '/(cris|ax|test)es$/i'      => "$1is",
            '/(shoe|foe)s$/i'               => "$1",
            '/(o)es$/i'                 => "$1",
            '/(bus)es$/i'               => "$1",
            '/([m|l])ice$/i'            => "$1ouse",
            '/(x|ch|ss|sh)es$/i'        => "$1",
            '/(m)ovies$/i'              => "$1ovie",
            '/(s)eries$/i'              => "$1eries",
            '/([^aeiouy]|qu)ies$/i'     => "$1y",
            '/([lr])ves$/i'             => "$1f",
            '/(tive)s$/i'               => "$1",
            '/(hive)s$/i'               => "$1",
            '/(li|wi|kni)ves$/i'        => "$1fe",
            '/(shea|loa|lea|thie)ves$/i'=> "$1f",
            '/(^analy)ses$/i'           => "$1sis",
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
            '/([ti])a$/i'               => "$1um",
            '/(n)ews$/i'                => "$1ews",
            '/(h|bl)ouses$/i'           => "$1ouse",
            '/(corpse)s$/i'             => "$1",
            '/(us)es$/i'                => "$1",
            '/s$/i'                     => ""
        );
    
    /**
     * Irregular words list. 
     *
     * @var array
     **/
    public static $irregular = array(
            'move'   => 'moves',
            'foot'   => 'feet',
            'goose'  => 'geese',
            'sex'    => 'sexes',
            'child'  => 'children',
            'man'    => 'men',
            'tooth'  => 'teeth',
            'person' => 'people',
            'complex' => 'complexes'
        );
        
    /**
     * Unaccountable words list. 
     *
     * @var array
     **/
    public static $uncountable = array(
            'data',
            'sheep',
            'fish',
            'deer',
            'series',
            'species',
            'money',
            'rice',
            'information',
            'equipment',
        );
    
    
    /**
     * Pluralizes English nouns.
     *
     * @param string $string English noun to pluralize.
     * @link http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
     * @return string
     **/
    public static function pluralize($string)
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
     * Singularizes English nouns.
     *
     * @param string $string English noun to singularize.
     * @link http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
     * @return string
     **/
    public static function singularize($string)
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
     * Converts an underscored or CamelCase word into a English sentence. The
     * titleize public function converts text like "WelcomePage",
     * "welcome_page" or  "welcome page" to this "Welcome Page". If second
     * parameter is set to 'first' it will only capitalize the first
     * character of the title.
     *
     * @param string $word Word to format as tile.
     * @param string $uppercase If set to 'first' it will only uppercase the
     * first character. Otherwise it will uppercase all the words in the title.
     * @return string
     **/
    public static function titleize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'first' ? 'ucfirst' : 'ucwords';
        return $uppercase(self::humanize(self::underscore($word)));
    }
    
    /**
     * Returns given word as CamelCased. Converts a word like "send_email" to
     * "SendEmail". It will remove non alphanumeric character from the word,
     * so "who's online" will be converted to "WhoSOnline".
     *
     * @param string $word
     * @param string $lowercamel
     * @return string
     **/
    public static function camelize($word, $lowercamel = false)
    {
        $word = str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',$word)));
        if ($lowercamel) {
            $word = strtolower(substr($word, 0, 1)) . substr($word, 1);
        }
        return $word;
    }
    
    /**
     * Converts a word "into_it_s_underscored_version".
     *
     * Convert any "CamelCased" or "ordinary Word" into an "underscored_word".
     * This can be really useful for creating friendly URLs.
     *
     * @param string $word
     * @param string $sep Default separator is an underscore ("_").
     * @param boolean $strtolower Default true
     * @return string
     **/
    public static function underscore($word, $sep = '_', $strtolower = true)
    {
        $sep = empty($sep) ? '_' : $sep;
        $return = preg_replace('/[^A-Z^a-z^0-9]+/', $sep,
                    preg_replace('/([a-z\d])([A-Z])/','\1_\2',
                        preg_replace('/([A-Z]+)([A-Z][a-z])/','\1_\2',$word)));
        return $strtolower ? strtolower($return) : $return;
    }
    
    /**
     * Returns a human-readable string from $word, by replacing underscores
     * with a space, and by upper-casing the initial character by default.
     * If you need to uppercase all the words you just have to pass 'all' as a
     * second parameter.
     *
     * @param string $word
     * @param boolean $ucwords
     * @return string
     **/
    public static function humanize($word, $ucwords = false)
    {
        $ucwords = $ucwords ? 'ucwords' : 'ucfirst';
        return $ucwords(str_replace('_',' ',preg_replace('/_id$/', '',$word)));
    }
    
    /**
     * Same as camelize but first char is underscored. Converts a word like
     * "send_email" to "sendEmail". It will remove non alphanumeric character
     * from the word, so "who's online" will be converted to "whoSOnline".
     *
     * @param string $word
     * @see camelize()
     * @return string
     **/
    public static function variablize($word)
    {
        return self::camelize($word, 1);
    }

    /**
     * Converts a class name to its table name according to rails naming
     * conventions. Converts "Person" to "people"
     *
     * @param string $class_name
     * @see classify()
     * @return string
     **/
    public function tableize($class_name)
    {
        return self::pluralize(self::underscore($class_name));
    }

    /**
     * Converts a table name to its class name according to rails naming
     * conventions. Converts "people" to "Person"
     *
     * @param string $table_name
     * @see tableize()
     * @return string
     **/
    public static function classify($table_name)
    {
        return self::camelize(self::singularize($table_name));
    }

    /**
     * Converts number to its ordinal English form. This method
     * converts 13 to 13th, 2 to 2nd, etc.
     *
     * @param string/integer $number
     * @return string
     * @author Nesbert Hidalgo
     **/
    public static function ordinalize($number)
    {
        if (in_array((intval($number) % 100),range(11,13))){
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
    
    /**
     * Create a path string from class name. This method converts
     * API_PersonSubclass to api/person_subclass
     *
     * @param string $class_name
     * @return string
     * @author Nesbert Hidalgo
     **/
    public static function patherize($class_name)
    {
        if (!(strpos($class_name, '_') === false)) {
            $folders = explode('_', $class_name);
            foreach ($folders as $k => $v) {
                $folders[$k] = self::underscore($v);
            }
            return implode(DS, $folders);
        } else {
            return self::underscore($class_name);
        }
    }
} // END class Inflector extends ModuleBase