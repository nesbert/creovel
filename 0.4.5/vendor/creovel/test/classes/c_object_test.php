<?php
/**
 * Unit tests for CObject object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    CObject
     * @access protected
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->o = new CObject;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->o);
    }

    public function testInitialize_parents()
    {
        $this->assertEquals(null, $this->o->initialize_parents());
    }

    public function test__clone()
    {
        $obj2 = clone $this->o;
        $this->assertEquals($obj2, $this->o);
    }

    public function test__call()
    {
        // cant test error function cause framework halts php on errors
    }

    public function test__toString()
    {
        $this->assertEquals('CObject', (string) $this->o);
    }

    public function testTo_string()
    {
        $this->assertEquals('CObject', $this->o->to_string());
        $this->assertEquals(get_class($this->o), $this->o->to_string());
    }

    public function testTo_string_path()
    {
        $this->assertEquals('c_object', $this->o->to_string_path());
    }

    public function testThrow_error()
    {
        // cant test error function cause framework halts php on errors
    }
    public function test_debug()
    {
        // todo
    }
    
    public function test_user_defined_constants()
    {
        // todo
    }
    
    public function test_ancestors()
    {
        // todo
    }
}
?>
