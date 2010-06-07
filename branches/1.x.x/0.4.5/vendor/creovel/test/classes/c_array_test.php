<?php
/**
 * Unit tests for CArray object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
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
    
    public function test_clean()
    {
        $a = array('post' => '<p>test</p><script>alert("test")</script>');
        $this->assertEquals(array('post' => 'testalert(&quot;test&quot;)'),CArray::clean($a));
        $this->assertEquals(array('post' => 'test'),CArray::clean($a, 4));
        $this->assertEquals(array('post' => '&lt;p&gt;tes'),CArray::clean($a, 12, '<p>'));
    }

    public function test_search()
    {
        $a = array(
            'a' => array(1,2,3),
            'b' => array(4,5,6),
            'c' => array(6,7,8),
            );
        $b = array(
            'x' => array(1,4,7),
            'y' => array(2,5,8),
            'z' => array(3,6,9),
            );
        $c = array($a, $b);
        $this->assertEquals($b, CArray::search('y', array(2,5,8), $c));
        $this->assertEquals($a, CArray::search('b', array(4,5,6), $c));
    }
}
?>
