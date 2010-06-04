<?php
/**
 * Unit tests for ActiveValidation object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActiveValidationTest extends PHPUnit_Framework_TestCase
{
    /**
     * ActiveValidation object.
     *
     * @var object
     **/
    protected $v = null;
    
    /**
     * Load ActiveValidation class.
     *
     * @return void
     **/
    public function setUp()
    {
        $this->v = new ActiveValidation;
    }
    
    /**
     * Clear ActiveValidation class and errors.
     *
     * @return void
     **/
    public function tearDown()
    {
        $this->v->clear_errors();
        unset($this->v);
    }
    
    public function test_add_error()
    {
        $this->v->add_error('test', 'error on line 1');
        $this->assertEquals('error on line 1', $GLOBALS['CREOVEL']['VALIDATION_ERRORS']['test']);
    }
    
    public function test_has_errors()
    {
        $this->assertEquals(false, $this->v->has_errors());
        $this->v->add_error('test', 'error on line 1');
        $this->assertEquals(true, $this->v->has_errors());
    }
    
    public function test_clear_errors()
    {
        $this->v->add_error('test', 'error on line 1');
        $this->v->add_error('test2', 'error on line 2');
        $this->assertEquals(2, count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']));
        $this->v->clear_errors();
        $this->assertEquals(array(), $GLOBALS['CREOVEL']['VALIDATION_ERRORS']);
    }
    
    public function test_validates_acceptance_of()
    {
        $this->assertEquals(true, $this->v->validates_acceptance_of('test', '1'));
        $this->assertEquals(true, $this->v->validates_acceptance_of('test', 1));
        $this->assertEquals(true, $this->v->validates_acceptance_of('test', true));
        $this->assertEquals(false, $this->v->validates_acceptance_of('test', 0));
        $this->assertEquals(false, $this->v->validates_acceptance_of('test', false));
    }
    
    public function test_validates_confirmation_of()
    {
        $this->assertEquals(true, $this->v->validates_confirmation_of('test', 'pass', null, null, 'pass'));
        $this->assertEquals(false, $this->v->validates_confirmation_of('test', 'old', null, null, 'new'));
    }
    
    public function test_validates_email_format_of()
    {
        // valid email
        $this->assertEquals(true, $this->v->validates_email_format_of('test', 'nesbert@test.com'));
        $this->assertEquals(true, $this->v->validates_email_format_of('test', 'Nesbert@test.com'));
        // valid email
        $this->assertEquals(true, $this->v->validates_email_format_of('test', 'nesbert@hit.me'));
        // invalid email
        $this->assertEquals(false, $this->v->validates_email_format_of('test', 'nesbert#gmail.com'));
        // invalid email
        $this->assertEquals(false, $this->v->validates_email_format_of('test', 'nesbert@test'));
        // no email passed
        $this->assertEquals(true, $this->v->validates_email_format_of('test', ''));
        // no email passed but required
        $this->assertEquals(false, $this->v->validates_email_format_of('test', '', null, true));
    }
    
    public function test_validates_format_of()
    {
        // text only
        $this->assertEquals(true, $this->v->validates_format_of('test', 'somevalue', null, null, '/[A-z]/'));
        // number only
        $this->assertEquals(false, $this->v->validates_format_of('test', '123d', null, null, '/^[0-9]*$/'));
    }
    
    public function test_validates_presence_of()
    {
        $this->assertEquals(true, $this->v->validates_presence_of('test', 'somevalue'));
        $this->assertEquals(false, $this->v->validates_presence_of('test', ''));
    }
    
    public function test_validates_numericality_of()
    {
        $this->assertEquals(true, $this->v->validates_numericality_of('test', '1234'));
        $this->assertEquals(true, $this->v->validates_numericality_of('test', 1234));
        $this->assertEquals(false, $this->v->validates_numericality_of('test', '1234a'));
        $this->assertEquals(false, $this->v->validates_numericality_of('test', 'abc'));
    }
    
    public function test_validates_length_of()
    {
        $this->assertEquals(true, $this->v->validates_length_of('test', 'abc', null, null, 0, 3));
        $this->assertEquals(false, $this->v->validates_length_of('test', 'abcde', null, null, 0, 3));
        $this->assertEquals(false, $this->v->validates_length_of('test', '', null, true, 0, 3));
    }
    
    public function test_validates_url_format_of()
    {
        $this->assertEquals(true, $this->v->validates_url_format_of('test', 'https://www.google.com'));
        $this->assertEquals(true, $this->v->validates_url_format_of('test', 'http://google.com'));
        $this->assertEquals(false, $this->v->validates_url_format_of('test', 'google.com'));
        $this->assertEquals(false, $this->v->validates_url_format_of('test', 1234));
        $this->assertEquals(false, $this->v->validates_url_format_of('test', 'abc'));
    }
    
    public function test_validate_field_by_bool()
    {
        $this->assertEquals(true, $this->v->validate_field_by_bool(true, 'test', true));
        $this->assertEquals(true, $this->v->validate_field_by_bool(true, 'test', 1));
        $this->assertEquals(true, $this->v->validate_field_by_bool(false, 'test', false));
        $this->assertEquals(true, $this->v->validate_field_by_bool(false, 'test', 0));
        $this->assertEquals(true, $this->v->validate_field_by_bool(false, 'test', '0'));
        $this->assertEquals(false, $this->v->validate_field_by_bool(true, 'test', false, null, true));
        $this->assertEquals(false, $this->v->validate_field_by_bool(false, 'test', '', null, true));
    }
} // END class ValidationTest extends PHPUnit_Framework_TestCase