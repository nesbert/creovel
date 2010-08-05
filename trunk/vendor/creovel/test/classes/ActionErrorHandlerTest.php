<?php
/**
 * Unit tests for ActionErrorHandler object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActionErrorHandlerTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var ActionErrorHandler
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new ActionErrorHandler;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {}

    public function testAdd()
    {
        $this->assertTrue(method_exists($this->o, 'add'));
        
        // unable to test do to program termination
        /*
        $this->expectOutputString("
[Application Error]------------------------------

Test error


");
        $this->o->add('Test error');
        */
    }

    public function testEmail()
    {
        // unable to test do to program termination
        $this->assertTrue(method_exists($this->o, 'email'));
    }
}
?>
