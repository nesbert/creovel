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

class ActiveRecordFieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ActiveRecordField
     */
    protected $o;
    protected $cols = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new ActiveRecordField;
        $this->cols = array(
            'adapter_type' => 'mysql',
            'type' => 'VARCHAR',
            'size' => 100,
            'null' => 'YES',
            'key' => 'PK',
            'key_name' => 'PRIMARY',
            'default' => '',
            'extra' => 'auto_increment'
            );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($cols);
        unset($o);
    }

    public function testInit_with_object()
    {
        $this->assertFalse($this->o->init_with_object($this->cols));
        $this->o->init_with_object((object) $this->cols);
        $this->assertEquals($this->cols['type'], $this->o->type);
        $this->assertTrue($this->o->null);
        $this->assertFalse($this->o->has_changed);
        $this->assertTrue($this->o->is_identity);
        $this->assertEquals($this->cols['key'], $this->o->key);
        $this->assertEquals($this->cols['key_name'], $this->o->key_name);
    }

    public function testInit_with_array()
    {
        $this->o->init_with_array($this->cols);
        $this->assertEquals($this->cols['type'], $this->o->type);
        $this->assertTrue($this->o->null);
        $this->assertFalse($this->o->has_changed);
        $this->assertTrue($this->o->is_identity);
        $this->assertEquals($this->cols['key'], $this->o->key);
        $this->assertEquals($this->cols['key_name'], $this->o->key_name);
    }
    
    public function testObject()
    {
        $this->o->init_with_array($this->cols);
        $o = ActiveRecordField::object($this->cols);
        $this->assertEquals($this->o, $o);
    }

}
?>
