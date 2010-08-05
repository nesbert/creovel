<?php
/**
 * Unit tests for Inflector object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class InflectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Inflector
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Inflector;
        $this->words = array();
        $this->words['regular'] = array(
                    'dog' => 'dogs',
                    'cat' => 'cats',
                    'user' => 'users',
                    'profile' => 'profiles',
                    'state' => 'states',
                    'country' => 'countries',
                    'news' => 'news',
                    'post' => 'posts',
                    'item' => 'items',
                    'order' => 'orders',
                    'record' => 'records',
                    'transaction' => 'transactions',
                    'employee' => 'employees',
                    'matrix' => 'matrices',
                    'octopus' => 'octopi',
                    'virus' => 'viruses',
                    'mouse' => 'mice',
                    'louse' => 'lice',
                    'tomato' => 'tomatoes',
                    'bath' => 'baths',
                    'hash' => 'hashes',
                    'day' => 'days',
                    'party' => 'parties',
                    'ox' => 'oxen',
                    'fix' => 'fixes',
                    'fixture' => 'fixtures',
                    'success' => 'successes',
                    'wife' => 'wives',
                    'knife' => 'knives',
                    'buffalo' => 'buffalos',
                    'thesis' => 'theses',
                    'analysis' => 'analyses',
                    'hive' => 'hives',
                    'rive' => 'rives',
                    'query' => 'queries',
                    'movie' => 'movies',
                    'box' => 'boxes',
                    'church' => 'churches',
                    'bus' => 'buses',
                    'halo' => 'halos',
                    'foe' => 'foes',
                    'shoe' => 'shoes',
                    'axis' => 'axes',
                    'person' => 'people',
                    'woman' => 'women',
                    'index' => 'indices',
                    'alias' => 'aliases',
                    'status' => 'statuses',
                    'quiz' => 'quizzes',
                    'complex' => 'complexes'
                    );
        
        $this->words['uncountable'] = array(
                    'data', 'equipment', 'information',
                    'rice', 'money', 'species',
                    'series', 'fish', 'sheep'
                    );
        
        $this->words['irregular'] = array(
                    'person' => 'people',
                    'man' => 'men',
                    'child' => 'children',
                    'sex' => 'sexes',
                    'move' => 'moves'
                    );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testPluralize()
    {
        foreach ($this->words as $type => $words) {
            switch ($type) {
                case 'regular':
                case 'irregular':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v, Inflector::pluralize($k));
                    }
                    break;
                case 'uncountable':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v, Inflector::pluralize($v));
                    }
                    break;
            }
        }
    }

    public function testSingularize()
    {
        foreach ($this->words as $type => $words) {
            switch ($type) {
                case 'regular':
                case 'irregular':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($k, Inflector::singularize($v));
                    }
                    break;
                case 'uncountable':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v, Inflector::singularize($v));
                    }
                    break;
            }
        }
    }

    public function testTitleize()
    {
        $this->assertEquals('To Infinity And Beyond',
            Inflector::titleize('to_infinity_and_beyond'));
        $this->assertEquals('To Infinity And Beyond',
            Inflector::titleize('to infinity and beyond'));
        $this->assertEquals('To Infinity And Beyond',
            Inflector::titleize('to-infinity-and-beyond'));
        $this->assertEquals('To Infinity And Beyond',
            Inflector::titleize('toInfinityAndBeyond'));
            
        $this->assertEquals('To infinity and beyond',
            Inflector::titleize('to_infinity_and_beyond', 'first'));
        $this->assertEquals('To infinity and beyond',
            Inflector::titleize('to infinity and beyond', 'first'));
        $this->assertEquals('To infinity and beyond',
            Inflector::titleize('to-infinity-and-beyond', 'first'));
        $this->assertEquals('To infinity and beyond',
            Inflector::titleize('toInfinityAndBeyond', 'first'));
    }

    public function testCamelize()
    {
        $this->assertEquals('SomeLongVariable',
            Inflector::camelize('some_long_variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::camelize('some long variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::camelize('some-long-variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::camelize('someLongVariable'));
            
        $this->assertEquals('someLongVariable',
            Inflector::camelize('some_long_variable', 1));
        $this->assertEquals('someLongVariable',
            Inflector::camelize('some long variable', 1));
        $this->assertEquals('someLongVariable',
            Inflector::camelize('some-long-variable', 1));
        $this->assertEquals('someLongVariable',
            Inflector::camelize('someLongVariable', 1));
    }

    public function testUnderscore()
    {
        $this->assertEquals('some_long_variable',
            Inflector::underscore('some_long_variable'));
        $this->assertEquals('some_long_variable',
            Inflector::underscore('some long variable'));
        $this->assertEquals('some_long_variable',
            Inflector::underscore('some-long-variable'));
        $this->assertEquals('some_long_variable',
            Inflector::underscore('someLongVariable'));
        $this->assertEquals('some_long_variable',
            Inflector::underscore('SomeLongVariable'));
        $this->assertEquals('some_s_long_variable',
            Inflector::underscore('Some\'s Long Variable'));
    }

    public function testHumanize()
    {
        $this->assertEquals('Some long variable',
            Inflector::humanize('some_long_variable'));
        $this->assertEquals('SOME LONG VARIABLE',
            Inflector::humanize('SOME_LONG_VARIABLE'));
        $this->assertEquals('Some Long Variable',
            Inflector::humanize('some_long_variable', 1));
    }

    public function testVariablize()
    {
        $this->assertEquals('someLongVariable',
            Inflector::variablize('some_long_variable'));
        $this->assertEquals('someLongVariable',
            Inflector::variablize('some long variable'));
        $this->assertEquals('someLongVariable',
            Inflector::variablize('some-long-variable'));
        $this->assertEquals('someLongVariable',
            Inflector::variablize('someLongVariable'));
        $this->assertEquals('someLongVariable',
            Inflector::variablize('someLongVariable'));
        $this->assertEquals('someLongVariable',
            Inflector::variablize('someLongVariable'));
        $this->assertEquals('whoSOnline',
            Inflector::variablize("who's online"));
    }

    public function testTableize()
    {
        $this->assertEquals('people',
            Inflector::tableize('Person'));
        foreach ($this->words as $type => $words) {
            switch ($type) {
                case 'regular':
                case 'irregular':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v,
                            Inflector::tableize(ucfirst($k)));
                    }
                    break;
                case 'uncountable':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v,
                            Inflector::tableize(ucfirst($v)));
                    }
                    break;
            }
        }
    }

    public function testClassify()
    {
        $this->assertEquals('SomeLongVariable',
            Inflector::classify('some_long_variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::classify('some long variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::classify('some-long-variable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::classify('someLongVariable'));
        $this->assertEquals('SomeLongVariable',
            Inflector::classify('SomeLongVariable'));
        $this->assertEquals('SomeSLongVariable',
            Inflector::classify('Some\'s Long Variable'));
    }

    public function testOrdinalize()
    {
        $this->assertEquals('1st',Inflector::ordinalize(1));
        $this->assertEquals('2nd',Inflector::ordinalize(2));
        $this->assertEquals('3rd',Inflector::ordinalize(3));
        $this->assertEquals('4th',Inflector::ordinalize(4));
        $this->assertEquals('5th',Inflector::ordinalize(5));
        $this->assertEquals('6th',Inflector::ordinalize(6));
        $this->assertEquals('7th',Inflector::ordinalize(7));
        $this->assertEquals('8th',Inflector::ordinalize(8));
        $this->assertEquals('9th',Inflector::ordinalize(9));
        $this->assertEquals('10th',Inflector::ordinalize(10));
        $this->assertEquals('11th',Inflector::ordinalize(11));
        $this->assertEquals('12th',Inflector::ordinalize(12));
        $this->assertEquals('21st',Inflector::ordinalize(21));
        $this->assertEquals('22nd',Inflector::ordinalize(22));
        $this->assertEquals('23rd',Inflector::ordinalize(23));
        $this->assertEquals('24th',Inflector::ordinalize(24));
        $this->assertEquals('100th',Inflector::ordinalize(100));
        $this->assertEquals('101st',Inflector::ordinalize(101));
        $this->assertEquals('102nd',Inflector::ordinalize(102));
        $this->assertEquals('103rd',Inflector::ordinalize(103));
        $this->assertEquals('104th',Inflector::ordinalize(104));
    }

    public function testPatherize()
    {
        $this->assertEquals('api/person_subclass',
            Inflector::patherize('API_PersonSubclass'));
    }
}
?>
