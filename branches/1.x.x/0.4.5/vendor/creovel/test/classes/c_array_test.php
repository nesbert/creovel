<?php
/**
 * Unit tests for CArray object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    CArray
     * @access protected
     */
    protected $a;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->a = new CArray(array('a', 'b', 'c', 'd', 'e', 'f'));
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

    public function testClear()
    {
        $this->assertEquals(array(), $this->a->clear());
        $this->assertEquals(array(), $this->a->value);
    }

    public function testFirst()
    {
        $this->assertEquals('a', $this->a->first());
    }

    public function testLast()
    {
        $this->assertEquals('f', $this->a->last());
    }

    public function testNext()
    {
        $this->assertEquals('a', $this->a->first());
        $this->assertEquals('b', $this->a->next());
        $this->assertEquals('c', $this->a->next());
        $this->assertEquals('d', $this->a->next());
        $this->assertEquals('e', $this->a->next());
        $this->assertEquals('f', $this->a->next());
        $this->assertFalse($this->a->next());
    }

    /**
     * @todo Implement testPrev().
     */
    public function testPrev()
    {
        $this->assertEquals('f', $this->a->last());
        $this->assertEquals('e', $this->a->prev());
        $this->assertEquals('d', $this->a->prev());
        $this->assertEquals('c', $this->a->prev());
        $this->assertEquals('b', $this->a->prev());
        $this->assertEquals('a', $this->a->prev());
        $this->assertFalse($this->a->prev());
    }
}
?>
