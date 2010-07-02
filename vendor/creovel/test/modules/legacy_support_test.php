<?php
/**
 * Unit tests for LegacySupport object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class LegacySupportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {}

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testInit()
    {
        $this->assertTrue(method_exists('LegacySupport', 'init'));
    }
    
    public function testFunctions()
    {
        LegacySupport::init();
        
        $this->assertTrue(function_exists('print_obj'));
        $this->assertTrue(function_exists('in_string'));
        $this->assertTrue(function_exists('gmdatetime'));
    }
}
?>
