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
        $this->fruits = array(
            'a' => 'Apple',
            'b' => 'Banana',
            'c' => 'Citrus',
            );
        $this->vegs = array(
            'a' => 'Asparagus',
            'b' => 'Broccoli',
            'c' => 'Carrots',
            );
        
        $this->o->load_item($this->fruits);
        $this->o->load_item($this->vegs);
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

    public function test__get()
    {
        foreach ($this->o as $k => $food) {
            if ($k == 0) {
                $this->assertEquals('Apple', $food->a);
                $this->assertEquals('Banana', $food->b);
                $this->assertEquals('Citrus', $food->c);
            }
            if ($k == 1) {
                $this->assertEquals('Asparagus', $food->a);
                $this->assertEquals('Broccoli', $food->b);
                $this->assertEquals('Carrots', $food->c);
            }
        }
    }

    public function test__clone()
    {
        $obj2 = clone $this->o;
        $this->assertEquals($obj2, $this->o);
        $obj2->load_item($this->fruits);
        foreach ($obj2 as $k => $food) {
            if ($k == 0 || $k == 2) {
                $this->assertEquals('Apple', $food->a);
                $this->assertEquals('Banana', $food->b);
                $this->assertEquals('Citrus', $food->c);
            }
            if ($k == 1) {
                $this->assertEquals('Asparagus', $food->a);
                $this->assertEquals('Broccoli', $food->b);
                $this->assertEquals('Carrots', $food->c);
            }
        }
    }

    public function test__call()
    {
        $this->markTestSkipped('Need to learn to catch framework.');
        //$this->expectOutputString('[Application Error]');
        // $o = new CObject;
        // $o->fail();
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

    /**
     * @todo Implement testThrow_error().
     */
    public function testThrow_error()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testReset().
     */
    public function testReset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRewind().
     */
    public function testRewind()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCurrent().
     */
    public function testCurrent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testKey().
     */
    public function testKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testNext().
     */
    public function testNext()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPrev().
     */
    public function testPrev()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValid().
     */
    public function testValid()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testInitialize_iterator().
     */
    public function testInitialize_iterator()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testHas_items().
     */
    public function testHas_items()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGet_items().
     */
    public function testGet_items()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testLoad_item().
     */
    public function testLoad_item()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testLoad_items().
     */
    public function testLoad_items()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCount().
     */
    public function testCount()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
