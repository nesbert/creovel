<?php
/**
 * Unit tests for CValidate object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CValidate
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new CValidate;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testHostname()
    {
        $this->assertTrue(CValidate::hostname('localhost'));
        $this->assertTrue(CValidate::hostname('www.creovel.ws'));
        $this->assertTrue(CValidate::hostname('creovel.local'));
        $this->assertFalse(CValidate::hostname('creovel.local#'));
    }

    public function testDomain()
    {
        $this->assertFalse(CValidate::domain('localhost'));
        $this->assertFalse(CValidate::domain('creovel.local'));
        $this->assertFalse(CValidate::domain('creovel.local#'));
        $this->assertTrue(CValidate::domain('creovel.org'));
        $this->assertTrue(CValidate::domain('creovel.com'));
        $this->assertTrue(CValidate::domain('creovel.info'));
        $this->assertTrue(CValidate::domain('creovel.mobi'));
        $this->assertFalse(CValidate::domain('_creovel.org'));
        $this->assertFalse(CValidate::domain('.creovel.org'));
        $this->assertTrue(CValidate::domain('www.creovel.org'));
        $this->assertTrue(CValidate::domain('test.creovel.org'));
        $this->assertTrue(CValidate::domain('test.test.creovel.org'));
    }

    public function testEmail()
    {
        $this->assertTrue(CValidate::email('test@creovel.org'));
        $this->assertTrue(CValidate::email('Test.Test@creovel.org'));
        $this->assertTrue(CValidate::email('Test.Test@test.creovel.org'));
        $this->assertTrue(CValidate::email('test.test@test.creovel.org'));
        $this->assertTrue(CValidate::email('1test.test@test.creovel.org'));
        $this->assertTrue(CValidate::email('test.test1@test.creovel.org'));
        $this->assertTrue(CValidate::email('1test.2test@test.creovel.org'));
        $this->assertTrue(CValidate::email('1test.2test@test1.creovel.org'));
        
        $this->assertTrue(CValidate::email('test@creovel.info'));
        $this->assertTrue(CValidate::email('Test.Test@creovel.info'));
        $this->assertTrue(CValidate::email('Test.Test@test.creovel.info'));
        $this->assertTrue(CValidate::email('test.test@test.creovel.info'));
        $this->assertTrue(CValidate::email('1test.test@test.creovel.info'));
        $this->assertTrue(CValidate::email('test.test1@test.creovel.info'));
        $this->assertTrue(CValidate::email('1test.2test@test.creovel.info'));
        $this->assertTrue(CValidate::email('1test.2test@test1.creovel.info'));
        
        $this->assertTrue(CValidate::email('test@creovel.us'));
        $this->assertTrue(CValidate::email('Test.Test@creovel.us'));
        $this->assertTrue(CValidate::email('Test.Test@test.creovel.us'));
        $this->assertTrue(CValidate::email('test.test@test.creovel.us'));
        $this->assertTrue(CValidate::email('1test.test@test.creovel.us'));
        $this->assertTrue(CValidate::email('test.test1@test.creovel.us'));
        $this->assertTrue(CValidate::email('1test.2test@test.creovel.us'));
        $this->assertTrue(CValidate::email('1test.2test@test1.creovel.us'));
        
        
        $this->assertFalse(CValidate::email('testcreovel.us'));
        $this->assertFalse(CValidate::email('test@creovel.toolong'));
        $this->assertFalse(CValidate::email('test.test@creovel.tooln'));
        $this->assertFalse(CValidate::email('test@_creovel.org'));
        $this->assertFalse(CValidate::email('@_creovel.org'));
        $this->assertFalse(CValidate::email('test&@creovel.org'));
    }

    public function testUrl()
    {
        $this->assertTrue(CValidate::url('http://creovel.org'));
        $this->assertTrue(CValidate::url('http://www.creovel.org'));
        $this->assertTrue(CValidate::url('https://creovel.org'));
        $this->assertTrue(CValidate::url('https://www.creovel.org'));
        $this->assertTrue(CValidate::url('http://localhost'));
        $this->assertTrue(CValidate::url('https://localhost'));
        $this->assertTrue(CValidate::url('ftp://creovel.org'));
        $this->assertTrue(CValidate::url('ftp://www.creovel.org'));
        $this->assertTrue(CValidate::url('ftp://localhost'));
        $this->assertTrue(CValidate::url('http://creovel.org/'));
        $this->assertTrue(CValidate::url('http://creovel.org/some/stuff'));
        $this->assertTrue(CValidate::url('http://localhost/some/stuff'));
        
        $this->assertFalse(CValidate::url('localhost'));
        $this->assertFalse(CValidate::url('creovel.org'));
        $this->assertFalse(CValidate::url('http:creovel.org'));
        $this->assertFalse(CValidate::url('https:creovel.org'));
        $this->assertFalse(CValidate::url('http:///creovel.org'));
        $this->assertFalse(CValidate::url('https:///creovel.org'));
        $this->assertFalse(CValidate::url('ftp:creovel.org'));
        $this->assertFalse(CValidate::url('ftp:creovel.org'));
    }

    public function testAlpha()
    {
        $this->assertTrue(CValidate::alpha('Test'));
        $this->assertTrue(CValidate::alpha('somereallongtexthere'));
        
        $this->assertFalse(CValidate::alpha('Test#1'));
        $this->assertFalse(CValidate::alpha('2Test'));
        $this->assertFalse(CValidate::alpha('Test Test'));
        $this->assertFalse(CValidate::alpha('&amp;'));
        $this->assertFalse(CValidate::alpha(123));
        $this->assertFalse(CValidate::alpha(true));
        $this->assertFalse(CValidate::alpha(false));
        $this->assertFalse(CValidate::alpha(1));
        $this->assertFalse(CValidate::alpha(0));
    }

    public function testAlpha_numeric()
    {
        $this->assertTrue(CValidate::alpha_numeric('Test'));
        $this->assertTrue(CValidate::alpha_numeric('somereallongtexthere'));
        $this->assertTrue(CValidate::alpha_numeric(123));
        $this->assertTrue(CValidate::alpha_numeric(1));
        $this->assertTrue(CValidate::alpha_numeric(0));
        
        $this->assertFalse(CValidate::alpha_numeric('Test#1'));
        $this->assertFalse(CValidate::alpha('2Test'));
        $this->assertFalse(CValidate::alpha_numeric('Test Test'));
        $this->assertFalse(CValidate::alpha_numeric('&amp;'));
        $this->assertFalse(CValidate::alpha_numeric(true));
        $this->assertFalse(CValidate::alpha_numeric(false));
    }

    public function testNumber()
    {
        $this->assertTrue(CValidate::number('123'));
        $this->assertTrue(CValidate::number('123.00'));
        $this->assertTrue(CValidate::number('123.001'));
        $this->assertTrue(CValidate::number(123));
        $this->assertTrue(CValidate::number(1));
        $this->assertTrue(CValidate::number(0));
        
        $this->assertFalse(CValidate::number('1e'));
        $this->assertFalse(CValidate::number('1,000,00.00'));
        $this->assertFalse(CValidate::number(array()));
    }

    public function testPositive_number()
    {
        $this->assertTrue(CValidate::positive_number('123'));
        $this->assertTrue(CValidate::positive_number('123.00'));
        $this->assertTrue(CValidate::positive_number('123.001'));
        $this->assertTrue(CValidate::positive_number(123));
        $this->assertTrue(CValidate::positive_number(1));
        
        $this->assertFalse(CValidate::positive_number(0));
        $this->assertFalse(CValidate::positive_number(-15));
        $this->assertFalse(CValidate::positive_number('-15'));
        $this->assertFalse(CValidate::positive_number('-15.00'));
        $this->assertFalse(CValidate::number('1e'));
        $this->assertFalse(CValidate::number('1,000,00.00'));
        $this->assertFalse(CValidate::number(array()));
    }

    public function testMatch()
    {
        $this->assertTrue(CValidate::match('123', '123'));
        $this->assertTrue(CValidate::match(123, 123));
        $this->assertTrue(CValidate::match(10.5, 10.50));
        $this->assertTrue(CValidate::match('test', 'test'));
        $this->assertTrue(CValidate::match(true, true));
        $this->assertTrue(CValidate::match(0, 0));
        $this->assertTrue(CValidate::match(false, false));
        
        $this->assertFalse(CValidate::match('123', 123));
        $this->assertFalse(CValidate::match(true, '1'));
        $this->assertFalse(CValidate::match(true, 1));
        $this->assertFalse(CValidate::match(true, 'test'));
        $this->assertFalse(CValidate::match('123', '1234'));
        $this->assertFalse(CValidate::match(123, 1234));
    }

    public function testBetween()
    {
        $this->assertTrue(CValidate::between('10', '1', '100'));
        $this->assertTrue(CValidate::between(10, 1, 100));
        
        $this->assertFalse(CValidate::between('101', '1', '100'));
        $this->assertFalse(CValidate::between(101, 1, 100));
    }

    public function testLength()
    {
        $array = array(1,2,3,4);
        $this->assertTrue(CValidate::length('test', '4'));
        $this->assertTrue(CValidate::length('test', 4));
        $this->assertTrue(CValidate::length($array, '4'));
        $this->assertTrue(CValidate::length($array, 4));
        
        $this->assertFalse(CValidate::length('test', '1'));
        $this->assertFalse(CValidate::length('test', 1));
        $this->assertFalse(CValidate::length($array, '5'));
        $this->assertFalse(CValidate::length($array, 5));
    }

    public function testLength_between()
    {
        $array = array(1,2,3,4);
        $this->assertTrue(CValidate::length_between('test', '4', '10'));
        $this->assertTrue(CValidate::length_between('test', 4, 10));
        $this->assertTrue(CValidate::length_between($array, '4', '10'));
        $this->assertTrue(CValidate::length_between($array, 4, 10));
        
        $this->assertFalse(CValidate::length_between('test', '1', '3'));
        $this->assertFalse(CValidate::length_between('test', 1, '3'));
        $this->assertFalse(CValidate::length_between($array, '5', '10'));
        $this->assertFalse(CValidate::length_between($array, 5, 10));
    }

    public function testRegex()
    {
        $this->assertTrue(CValidate::regex('/creovel/i'));
        $this->assertTrue(CValidate::regex('@creovel@'));
        $this->assertTrue(CValidate::regex('/^[0-9]/'));
        
        $this->assertFalse(CValidate::regex('Test#1'));
        $this->assertFalse(CValidate::regex('2Test'));
        $this->assertFalse(CValidate::regex('Test Test'));
        $this->assertFalse(CValidate::regex('123456'));
        $this->assertFalse(CValidate::regex(123456));
    }

    public function testEven()
    {
        $this->assertTrue(CValidate::even(0));
        $this->assertTrue(CValidate::even('2'));
        $this->assertTrue(CValidate::even(2));
        $this->assertTrue(CValidate::even(4));
        $this->assertTrue(CValidate::even('6'));
        $this->assertTrue(CValidate::even(6));
        $this->assertTrue(CValidate::even(8));
        $this->assertTrue(CValidate::even(10));
        $this->assertTrue(CValidate::even(20));
        $this->assertTrue(CValidate::even(30));
        $this->assertTrue(CValidate::even(50));
        $this->assertTrue(CValidate::even(88));
        $this->assertTrue(CValidate::even(100));
        $this->assertTrue(CValidate::even(1000000));
        
        $this->assertFalse(CValidate::even('1'));
        $this->assertFalse(CValidate::even(1));
        $this->assertFalse(CValidate::even(3));
        $this->assertFalse(CValidate::even(5));
        $this->assertFalse(CValidate::even(7));
        $this->assertFalse(CValidate::even('5'));
        $this->assertFalse(CValidate::even(5));
        $this->assertFalse(CValidate::even(55));
        $this->assertFalse(CValidate::even(99));
        $this->assertFalse(CValidate::even(101));
        $this->assertFalse(CValidate::even(1000001));
        $this->assertFalse(CValidate::even('test123'));
    }

    public function testOdd()
    {
        $this->assertTrue(CValidate::odd('1'));
        $this->assertTrue(CValidate::odd(1));
        $this->assertTrue(CValidate::odd(3));
        $this->assertTrue(CValidate::odd(5));
        $this->assertTrue(CValidate::odd(7));
        $this->assertTrue(CValidate::odd('5'));
        $this->assertTrue(CValidate::odd(5));
        $this->assertTrue(CValidate::odd(55));
        $this->assertTrue(CValidate::odd(99));
        $this->assertTrue(CValidate::odd(101));
        $this->assertTrue(CValidate::odd(1000001));
        
        $this->assertFalse(CValidate::odd(0));
        $this->assertFalse(CValidate::odd('2'));
        $this->assertFalse(CValidate::odd(2));
        $this->assertFalse(CValidate::odd(4));
        $this->assertFalse(CValidate::odd('6'));
        $this->assertFalse(CValidate::odd(6));
        $this->assertFalse(CValidate::odd(8));
        $this->assertFalse(CValidate::odd(10));
        $this->assertFalse(CValidate::odd(20));
        $this->assertFalse(CValidate::odd(30));
        $this->assertFalse(CValidate::odd(50));
        $this->assertFalse(CValidate::odd(88));
        $this->assertFalse(CValidate::odd(100));
        $this->assertFalse(CValidate::odd(1000000));
        $this->assertFalse(CValidate::odd('test123'));
    }

    public function testAjax()
    {
        $this->assertFalse(CValidate::ajax());
        $_REQUEST['_AJAX_'] = 1;
        $this->assertTrue(CValidate::ajax());
    }

    public function testHash()
    {
        $this->assertTrue(CValidate::hash(array('test' => 1234)));
        $this->assertTrue(CValidate::hash(array('test' => 1234, '123')));
        
        $this->assertFalse(CValidate::hash(array(1,2,3,4)));
        $this->assertFalse(CValidate::hash('1234'));
        $this->assertFalse(CValidate::hash(1234));
    }

    public function testIn_string()
    {
        $this->assertTrue(CValidate::in_string('creo', 'creovel'));
        $this->assertTrue(CValidate::in_string(1, '123456'));
        
        $this->assertFalse(CValidate::in_string('test', 'creovel'));
        $this->assertFalse(CValidate::in_string(10, '123456'));
    }
}
?>
