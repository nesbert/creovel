<?

class inflector_test extends unittest
{
	public $singluar_to_plural = array
	(
		"search"      => "searches",
		"switch"      => "switches",
		"fix"         => "fixes",
		"box"         => "boxes",
		"process"     => "processes",
		"address"     => "addresses",
		"case"        => "cases",
		"stack"       => "stacks",
		"wish"        => "wishes",
		"fish"        => "fish",

		"category"    => "categories",
		"query"       => "queries",
		"ability"     => "abilities",
		"agency"      => "agencies",
		"movie"       => "movies",

		"archive"     => "archives",

		"index"       => "indices",

		"wife"        => "wives",
		"safe"        => "saves",
		"half"        => "halves",

		"move"        => "moves",

		"salesperson" => "salespeople",
		"person"      => "people",

		"spokesman"   => "spokesmen",
		"man"         => "men",
		"woman"       => "women",

		"basis"       => "bases",
		"diagnosis"   => "diagnoses",

		"medium"      => "media",
		"analysis"    => "analyses",

		"node_child"  => "node_children",
		"child"       => "children",

		"experience"  => "experiences",
		"day"         => "days",

		"comment"     => "comments",
		"foobar"      => "foobars",
		"newsletter"  => "newsletters",

		"old_news"    => "old_news",
		"news"        => "news",

		"series"      => "series",
		"species"     => "species",

		"quiz"        => "quizzes",

		"perspective" => "perspectives",

		"ox"          => "oxen",
		"photo"       => "photos",
		"buffalo"     => "buffaloes",
		"tomato"      => "tomatoes",
		"dwarf"       => "dwarves",
		"elf"         => "elves",
		"information" => "information",
		"equipment"   => "equipment",
		"bus"         => "buses",
		"status"      => "statuses",
		"status_code" => "status_codes",
		"mouse"       => "mice",

		"louse"       => "lice",
		"house"       => "houses",
		"octopus"     => "octopi",
		"virus"       => "viri",
		"alias"       => "aliases",
		"portfolio"   => "portfolios",

		"vertex"      => "vertices",
		"matrix"      => "matrices",

		"axis"        => "axes",
		"testis"      => "testes",
		"crisis"      => "crises",

		"rice"        => "rice",
		"shoe"        => "shoes",

		"horse"       => "horses",
		"prize"       => "prizes",
		"edge"        => "edges"
	);

	public $camel_to_underscore = array
	(
		"Product"               => "product",
		"SpecialGuest"          => "special_guest",
		"ApplicationController" => "application_controller",
		"Area51Controller"      => "area51_controller"
	);

	public $underscore_to_camel = array
	(
		"product"                => "Product",
		"special_guest"          => "SpecialGuest",
		"application_controller" => "ApplicationController",
		"area51_controller"      => "Area51Controller"
	);
	
	public $underscore_to_lower_camel = array
	(
		"product"                => "product",
		"special_guest"          => "specialGuest",
		"application_controller" => "applicationController",
		"area51_controller"      => "area51Controller"
	);

	public $camel_to_underscore_without_reverse = array
	(
		"HTMLTidy"              => "html_tidy",
		"HTMLTidyGenerator"     => "html_tidy_generator",
		"FreeBSD"               => "free_bsd",
		"HTML"                  => "html",
	);

	public $underscore_to_human = array
	(
		"employee_salary" 		=> "Employee salary",
		"employee_id"     		=> "Employee",
		"underground"     		=> "Underground"
	);

	public $ordinal_numbers = array
	(
		"0" => "0th",
		"1" => "1st",
		"2" => "2nd",
		"3" => "3rd",
		"4" => "4th",
		"5" => "5th",
		"6" => "6th",
		"7" => "7th",
		"8" => "8th",
		"9" => "9th",
		"10" => "10th",
		"11" => "11th",
		"12" => "12th",
		"13" => "13th",
		"14" => "14th",
		"20" => "20th",
		"21" => "21st",
		"22" => "22nd",
		"23" => "23rd",
		"24" => "24th",
		"100" => "100th",
		"101" => "101st",
		"102" => "102nd",
		"103" => "103rd",
		"104" => "104th",
		"110" => "110th",
		"111" => "111th",
		"112" => "112th",
		"113" => "113th",
		"1000" => "1000th",
		"1001" => "1001st"
	);

	public function setup()
	{
		$this->inflector = new inflector();
	}

	public function teardown()
	{
		$this->inflector = null;
	}

	public function test_pluralize_plurals()
	{
		$this->assert_equal('plurals', $this->inflector->pluralize('plurals'));
		$this->assert_equal('Plurals', $this->inflector->pluralize('Plurals'));
	}

	public function test_uncountables()
	{
		foreach (array( 'data', 'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' ) as $word)
		{
    		$this->assert_equal($word, $this->inflector->pluralize($word));
    		$this->assert_equal($word, $this->inflector->singularize($word));
		}
	}

	public function test_pluralize()
	{
		foreach ($this->singluar_to_plural as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->pluralize($k));
		}
	}

	public function test_singularize()
	{
		foreach ($this->singluar_to_plural as $k => $v)
		{
    		$this->assert_equal($k, $this->inflector->singularize($v));
		}
	}

	public function test_underscore()
	{
		foreach ($this->camel_to_underscore as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->underscore($k));
		}

		foreach ($this->camel_to_underscore_without_reverse as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->underscore($k));
		}
	}

	public function test_camelize()
	{
		foreach ($this->underscore_to_lower_camel as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->camelize($k, true));
		}

		foreach ($this->underscore_to_camel as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->camelize($k));
		}
	}

	public function test_humanize()
	{
		foreach ($this->underscore_to_human as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->humanize($k));
		}
	}

	public function test_ordinalize()
	{
		foreach ($this->ordinal_numbers as $k => $v)
		{
    		$this->assert_equal($v, $this->inflector->ordinalize($k));
		}
	}
}

?>
