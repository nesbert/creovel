<?php
/**
 * Unit tests for CString object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CStringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    CString
     * @access protected
     */
    protected $s;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->s = new CString;
        
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
                        $this->assertEquals($v, CString::pluralize($k, 0));
                        $this->assertEquals($k, CString::pluralize($k, 1));
                        $this->assertEquals($v, CString::pluralize($k, 2));
                        $this->assertEquals($v, CString::pluralize($k, 101));
                        $this->assertEquals($v, CString::pluralize($k, -1));
                    }
                    break;
                case 'uncountable':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v, CString::pluralize($v, 0));
                        $this->assertEquals($v, CString::pluralize($v, 1));
                        $this->assertEquals($v, CString::pluralize($v, 2));
                        $this->assertEquals($v, CString::pluralize($v, 101));
                        $this->assertEquals($v, CString::pluralize($v, -1));
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
                        $this->assertEquals($k, CString::singularize($v));
                    }
                    break;
                case 'uncountable':
                    foreach ($words as $k => $v) {
                        $this->assertEquals($v, CString::singularize($v));
                    }
                    break;
            }
        }
    }

    public function testHumanize()
    {
        $this->assertEquals('Some long variable',
            CString::humanize('some_long_variable'));
        $this->assertEquals('SOME LONG VARIABLE',
            CString::humanize('SOME_LONG_VARIABLE'));
        $this->assertEquals('Some Long Variable',
            CString::humanize('some_long_variable', 1));
    }

    public function testCamelize()
    {
        $this->assertEquals('SomeLongVariable',
            CString::camelize('some_long_variable'));
        $this->assertEquals('SomeLongVariable',
            CString::camelize('some long variable'));
        $this->assertEquals('SomeLongVariable',
            CString::camelize('some-long-variable'));
        $this->assertEquals('SomeLongVariable',
            CString::camelize('someLongVariable'));
            
        $this->assertEquals('someLongVariable',
            CString::camelize('some_long_variable', 1));
        $this->assertEquals('someLongVariable',
            CString::camelize('some long variable', 1));
        $this->assertEquals('someLongVariable',
            CString::camelize('some-long-variable', 1));
        $this->assertEquals('someLongVariable',
            CString::camelize('someLongVariable', 1));
    }

    public function testDasherize()
    {
        $this->assertEquals('some-long-variable',
            CString::dasherize('some_long_variable'));
        $this->assertEquals('some-long-variable',
            CString::dasherize('some long variable'));
        $this->assertEquals('some-long-variable',
            CString::dasherize('some-long-variable'));
        $this->assertEquals('some-long-variable',
            CString::dasherize('someLongVariable'));
        $this->assertEquals('some-s-long-variable',
            CString::dasherize('Some\'s Long Variable'));
            
        $this->assertEquals('some/long/variable',
            CString::dasherize('some_long_variable', '/'));
        $this->assertEquals('some/long/variable',
            CString::dasherize('some long variable', '/'));
        $this->assertEquals('some/long/variable',
            CString::dasherize('some-long-variable', '/'));
        $this->assertEquals('some/long/variable',
            CString::dasherize('someLongVariable', '/'));
        $this->assertEquals('some/s/long/variable',
            CString::dasherize('Some\'s Long Variable', '/'));
    }

    public function testUnderscore()
    {
        $this->assertEquals('some_long_variable',
            CString::underscore('some_long_variable'));
        $this->assertEquals('some_long_variable',
            CString::underscore('some long variable'));
        $this->assertEquals('some_long_variable',
            CString::underscore('some-long-variable'));
        $this->assertEquals('some_long_variable',
            CString::underscore('someLongVariable'));
        $this->assertEquals('some_long_variable',
            CString::underscore('SomeLongVariable'));
        $this->assertEquals('some_s_long_variable',
            CString::underscore('Some\'s Long Variable'));
    }

    public function testClassify()
    {
        $this->assertEquals('SomeLongVariable',
            CString::classify('some_long_variable'));
        $this->assertEquals('SomeLongVariable',
            CString::classify('some long variable'));
        $this->assertEquals('SomeLongVariable',
            CString::classify('some-long-variable'));
        $this->assertEquals('SomeLongVariable',
            CString::classify('someLongVariable'));
        $this->assertEquals('SomeLongVariable',
            CString::classify('SomeLongVariable'));
        $this->assertEquals('SomeSLongVariable',
            CString::classify('Some\'s Long Variable'));
    }

    public function testCycle()
    {
        $this->assertEquals('1', CString::cycle());
        $this->assertEquals('2', CString::cycle());
        $this->assertEquals('1', CString::cycle());
        $this->assertEquals('2', CString::cycle());
        $this->assertEquals('row-1', CString::cycle('row-1', 'row-2'));
        $this->assertEquals('row-2', CString::cycle('row-1', 'row-2'));
        $this->assertEquals('row-1', CString::cycle('row-1', 'row-2'));
        $this->assertEquals('row-2', CString::cycle('row-1', 'row-2'));
        $this->assertEquals('row-1', CString::cycle('row-1', 'row-2'));
        $this->assertEquals('row-1', CString::cycle('row-1', 'row-2', 1));
    }

    public function testQuote2string()
    {
        $this->assertEquals('&quot;who da man?&quot;',
            CString::quote2string('"who da man?"'));
        $this->assertEquals("what's that?",
            CString::quote2string("what's that?"));
    }

    public function testMask()
    {
        $this->assertEquals('****', CString::mask('test'));
        $this->assertEquals('#####', CString::mask('tests', '#'));
    }

    public function testTruncate()
    {
        $this->assertEquals('This test...',
            CString::truncate('This test has not been implemented yet.', 10));
        $this->assertEquals('This te***',
            CString::truncate('This test has not been implemented yet.', 10, '***', 1));
    }

    public function testWordwrap_line()
    {
        $t = 'My very educated mother just served us nine pizzas.';
        $this->assertEquals(implode("\n", explode(' ', $t)),
            CString::wordwrap_line($t, 1));
        $this->assertEquals(
            "My very\neducated\nmother just\nserved us\nnine pizzas.",
            CString::wordwrap_line($t, 10));
        $this->assertEquals(
            "My very educated\nmother just served us\nnine pizzas.",
            CString::wordwrap_line($t, 20));
    }

    public function testRetrieve_number()
    {
        $this->assertEquals(2, CString::retrieve_number('2 records'));
        $this->assertEquals(100, CString::retrieve_number('there was 100 records'));
    }

    public function testStarts_with()
    {
        $this->assertTrue(CString::starts_with('foo f', 'foo foo'));
        $this->assertTrue(CString::starts_with('bar', 'bar bar'));
        $this->assertFalse(CString::starts_with('foo F', 'foo foo'));
    }

    public function testEnds_with()
    {
        $this->assertTrue(CString::ends_with(' foo', 'foo foo'));
        $this->assertTrue(CString::ends_with('ar', 'bar bar'));
        $this->assertFalse(CString::ends_with('o FOO', 'foo foo'));
    }

    public function testNum2words()
    {
        $this->assertEquals('one hundred and 00/100',
            CString::num2words('100'));
        $this->assertEquals('one hundred',
            CString::num2words('100', 0, 0, 0));
        $this->assertEquals('One Hundred',
            CString::num2words('100', 0, 1, 0));
        $this->assertEquals('one hundred dollars and 00 cents',
            CString::num2words('100' , 1));
        $this->assertEquals(
            'ten million twenty-five thousand twenty and 50/100',
            CString::num2words(10025020.50));
    }

    public function testEscape_string()
    {
        // need todo
    }

    public function testSplit_words()
    {
        $t = 'My very educated mother just served us nine pizzas.';
        $this->assertEquals(explode(' ', $t),
            CString::split_words($t));
        $this->assertEquals(array(
                'My very','educated',
                'mother','just','served us','nine','pizzas.'
                ),
            CString::split_words($t, 10));
        $this->assertEquals(array(
                'My very educated','mother just served','us nine pizzas.'
                ),
            CString::split_words($t, 20));
    }

    public function testCapitalize()
    {
        $this->assertEquals('Abc abc',
            CString::capitalize('abc abc'));
        $this->assertEquals('United states of america',
            CString::capitalize('united states of america'));
    }

    public function testContains()
    {
        $this->assertTrue(CString::contains('@', '@creovel'));
        $this->assertTrue(CString::contains('ok', 'test tested ok today'));
    }

    public function testEscape_HTML()
    {
        $this->assertEquals('&amp;', CString::escape_HTML('&'));
        $this->assertEquals('&lt;p&gt;', CString::escape_HTML('<p>'));
    }

    public function testGsub()
    {
        $this->assertEquals('1bc1bc', CString::gsub('abcabc', 'a', 1));
        $this->assertEquals('ABcABc', CString::gsub('abcabc', 'ab', 'AB'));
    }

    public function testIs_empty()
    {
        $this->assertTrue(CString::is_empty(null));
        $this->assertTrue(CString::is_empty(0));
        $this->assertTrue(CString::is_empty(0.00));
        $this->assertTrue(CString::is_empty('0'));
        $this->assertTrue(CString::is_empty(array()));
        $this->assertTrue(CString::is_empty(''));
        $this->assertFalse(CString::is_empty(' '));
    }

    public function testStrip()
    {
        $this->assertEquals('', CString::strip(' '));
        $this->assertEquals('', CString::strip('     '));
        $this->assertEquals('test', CString::strip(' test     '));
        $this->assertEquals('test', CString::strip('test     '));
        $this->assertEquals('test', CString::strip(' test'));
    }

    public function testStrip_scripts()
    {
        $text = '<p>test</p><script>alert("hello");</script><p>test</p>';
        $this->assertEquals(
            '<p>test</p><p>test</p>',
            CString::strip_scripts($text));
    }

    public function testStrip_tags()
    {
        $text = '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>';
        $this->assertEquals('MVC', CString::strip_tags("<p><b>MVC</b></p>"));
        $this->assertEquals(
            'Test paragraph. Other text',
            CString::strip_tags($text));
        $this->assertEquals(
            '<p>Test paragraph.</p> <a href="#fragment">Other text</a>',
             CString::strip_tags($text, '<p><a>'));
    }

    public function testSub()
    {
        $this->assertEquals('1bcabc', CString::sub('abcabc', 'a', 1));
        $this->assertEquals('1bc1bc', CString::sub('abcabc', 'a', 1, 2));
    }

    public function testTimes()
    {
        $this->assertEquals('aaaaaa', CString::times('a', 6));
        $this->assertEquals('abcabcabc', CString::times('abc', 3));
    }

    public function testTo_array()
    {
        $this->assertEquals(array('a','b','c'), CString::to_array('abc'));
    }

    public function test_prep_javascript()
    {
        $this->assertEquals('\n', CString::prep_javascript("\r\n"));
        $this->assertEquals('\n', CString::prep_javascript("\r"));
        $this->assertEquals('\n', CString::prep_javascript("\n"));
        $this->assertEquals('\\\\"', CString::prep_javascript('\"'));
    }

    public function testUnescape_html()
    {
        $this->assertEquals('<p>', CString::unescape_html("&lt;p&gt;"));
        $this->assertEquals('&', CString::unescape_html("&amp;"));
    }
    
    public function test_replace_with_array()
    {
        $s = 'abcdefghijklmnopqrstuvwxyz';
        $this->assertEquals(
            'abcdEfghijklmNopqrStuvwxyz',
            CString::replace_with_array($s, array('n' => 'N', 'e' => 'E', 's' => 'S')));
        $this->assertEquals(
            'abcdefghijkl | mnopqrstuvwxyz',
            CString::replace_with_array($s, array('mno' => ' | mno')));
    }
    
    public function test_clean()
    {
        $s = '<p>test</p><script>alert("test")</script>';
        $this->assertEquals('testalert(&quot;test&quot;)',CString::clean($s));
        $this->assertEquals('test',CString::clean($s, 4));
        $this->assertEquals('&lt;p&gt;tes',CString::clean($s, 12, '<p>'));
    }
}
?>
