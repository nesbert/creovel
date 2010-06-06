<?php
/**
 * Unit tests for CLocale object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CLocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CLocale
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CLocale;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCountries_array()
    {
        $this->assertFalse(isset($GLOBALS['CREOVEL']['COUNTRIES']));
        
        $countries = CLocale::countries_array();
        $this->assertTrue(is_array($countries));
        $this->assertEquals(50, count($countries['US']['states']));
        
        $countries = CLocale::countries_array(true);
        
        $this->assertTrue(is_array($countries));
        $this->assertTrue(count($countries['US']['states']) > 50);
        
        $this->assertTrue(isset($GLOBALS['CREOVEL']['COUNTRIES']));
    }

    public function testCountries()
    {
        $this->assertFalse(isset($GLOBALS['CREOVEL']['COUNTRIES']));
        
        $countries = CLocale::countries();
        $this->assertTrue(isset($countries['US']));
        $this->assertTrue(isset($countries['CA']));
        $this->assertEquals('United States', $countries['US']);
        $this->assertEquals('Canada', $countries['CA']);
        $this->assertEquals('Afghanistan', current($countries));
        $this->assertEquals('Zimbabwe', end($countries));
        
        $countries = CLocale::countries(true, true);
        $this->assertEquals('US - United States', current($countries));
        $this->assertEquals('CA - Canada', next($countries));
        $this->assertEquals('US - United States', $countries['US']);
        $this->assertEquals('CA - Canada', $countries['CA']);
        
        $this->assertTrue(isset($GLOBALS['CREOVEL']['COUNTRIES']));
    }

    public function testStates()
    {
        $s = CLocale::states();
        $this->assertTrue(is_array($s));
        $this->assertEquals(50, count($s));
        $this->assertEquals('Alabama', current($s));
        $this->assertEquals('Wyoming', end($s));
        
        $s = CLocale::states('CA');
        $this->assertTrue(is_array($s));
        $this->assertEquals(13, count($s));
        $this->assertEquals('Alberta', current($s));
        $this->assertEquals('Yukon Territory', end($s));
        
        $s = CLocale::states('US', true);
        $this->assertTrue(is_array($s));
        $this->assertEquals(50, count($s));
        $this->assertEquals('AL - Alabama', current($s));
        $this->assertEquals('WY - Wyoming', end($s));
        
        $s = CLocale::states('US', false, true);
        
        $this->assertTrue(is_array($s));
        $this->assertTrue(count($s) > 50);
        $this->assertEquals('Alabama', current($s));
        $this->assertEquals('Military Pacific', end($s));
    }

    /**
     * @todo Implement testTimezones().
     */
    public function testTimezones()
    {
        $this->assertFalse(isset($GLOBALS['CREOVEL']['TIMEZONES']));
        
        $t = CLocale::timezones();
        $this->assertTrue(is_array($t));
        $this->assertEquals(78, count($t));
        
        $this->assertTrue(isset($GLOBALS['CREOVEL']['TIMEZONES']));
    }
}
?>
