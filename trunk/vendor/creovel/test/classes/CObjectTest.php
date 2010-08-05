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

class CObjectTest extends PHPUnit_Extensions_OutputTestCase
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
        $a = array(1,2,3,4,5,6);
        $o = '<pre class="debug">
Array
(
    [0] => 1
    [1] => 2
    [2] => 3
    [3] => 4
    [4] => 5
    [5] => 6
)
</pre>
';
        $this->expectOutputString($o);
        CObject::debug($a);
    }
    
    public function test_user_defined_constants()
    {
        $a = CObject::user_defined_constants();
        $this->assertTrue(in_array('BASE_PATH', array_keys($a)));
        $this->assertTrue(in_array('DS', array_keys($a)));
        $this->assertTrue(in_array('CONFIG_PATH', array_keys($a)));
        $this->assertTrue(in_array('PUBLIC_PATH', array_keys($a)));
        $this->assertTrue(in_array('APP_PATH', array_keys($a)));
        $this->assertTrue(in_array('LOG_PATH', array_keys($a)));
        $this->assertTrue(in_array('MODELS_PATH', array_keys($a)));
        $this->assertTrue(in_array('VIEWS_PATH', array_keys($a)));
        $this->assertTrue(in_array('CONTROLLERS_PATH', array_keys($a)));
        $this->assertTrue(in_array('HELPERS_PATH', array_keys($a)));
        $this->assertTrue(in_array('SCRIPT_PATH', array_keys($a)));
        $this->assertTrue(in_array('VENDOR_PATH', array_keys($a)));
        $this->assertTrue(in_array('CREOVEL_PATH', array_keys($a)));
        $this->assertTrue(in_array('CSS_URL', array_keys($a)));
        $this->assertTrue(in_array('JAVASCRIPT_URL', array_keys($a)));
        $this->assertTrue(in_array('CREOVEL_VERSION', array_keys($a)));
        $this->assertTrue(in_array('CREOVEL_RELEASE_DATE', array_keys($a)));
        $this->assertTrue(in_array('PHP', array_keys($a)));
        $this->assertTrue(in_array('SECOND', array_keys($a)));
        $this->assertTrue(in_array('MINUTE', array_keys($a)));
        $this->assertTrue(in_array('HOUR', array_keys($a)));
        $this->assertTrue(in_array('DAY', array_keys($a)));
        $this->assertTrue(in_array('WEEK', array_keys($a)));
        $this->assertTrue(in_array('MONTH', array_keys($a)));
        $this->assertTrue(in_array('YEAR', array_keys($a)));
    }
    
    public function test_ancestors()
    {
        $a = CObject::ancestors('IndexController');
        $this->assertEquals('IndexController', $a[0]);
        $this->assertEquals('ApplicationController', $a[1]);
        $this->assertEquals('ActionController', $a[2]);
        $this->assertEquals('CObject', $a[3]);
        
        $a = CObject::ancestors('ActiveRecord');
        $this->assertEquals('ActiveRecord', $a[0]);
        $this->assertEquals('CObject', $a[1]);
        
        $a = CObject::ancestors('Mysql');
        $this->assertEquals('Mysql', $a[0]);
        $this->assertEquals('AdapterBase', $a[1]);
        $this->assertEquals('CObject', $a[2]);
    }
}
?>
