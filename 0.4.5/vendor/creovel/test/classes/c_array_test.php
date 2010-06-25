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
        $this->og = array('a', 'b', 'c', 'd', 'e', 'f');
        $this->a = new CArray($this->og);
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

    public function testPop()
    {
        $r = $this->a->pop();
        $this->assertEquals('f', $r);
        $r = array_pop($this->og);
        $this->assertEquals($this->og, $this->a->value);
    }

    public function testPush()
    {
        $this->assertEquals(1, $this->a->push('g'));
        $r = array_push($this->og, 'g');
        $this->assertEquals($this->og, $this->a->value);
        $this->assertEquals(3, $this->a->push('h', 'i', 'j'));
        $r = array_push($this->og, 'h', 'i', 'j');
        $this->assertEquals($this->og, $this->a->value);
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
    
    public function test_rewind()
    {
        $this->assertEquals('a', $this->a->first());
        $this->assertEquals('b', $this->a->next());
        $this->assertEquals('c', $this->a->next());
        $this->assertEquals('d', $this->a->next());
        $this->assertEquals('a', $this->a->rewind());
        $this->assertEquals('b', $this->a->next());
        $this->assertEquals('c', $this->a->next());
        $this->assertEquals('a', $this->a->rewind());
    }

    public function test_current()
    {
        $this->assertEquals('a', $this->a->current());
        $this->assertEquals('b', $this->a->next());
        $this->assertEquals('c', $this->a->next());
        $this->assertEquals('d', $this->a->next());
        $this->assertEquals('d', $this->a->current());
        $this->assertEquals('e', $this->a->next());
        $this->assertEquals('f', $this->a->next());
        $this->assertEquals('f', $this->a->current());
    }
    
    public function test_key()
    {
        $this->assertEquals(0, $this->a->key());
        $this->assertEquals('a', $this->a->current());
        $this->a->next();
        $this->assertEquals(1, $this->a->key());
        $this->assertEquals('b', $this->a->current());
        $this->a->next();
        $this->assertEquals(2, $this->a->key());
        $this->assertEquals('c', $this->a->current());
        $this->a->next();
        $this->assertEquals(3, $this->a->key());
        $this->assertEquals('d', $this->a->current());
        $this->a->next();
        $this->assertEquals(4, $this->a->key());
        $this->assertEquals('e', $this->a->current());
        $this->a->rewind();
        $this->assertEquals(0, $this->a->key());
        $this->assertEquals('a', $this->a->current());
    }
    
    public function test_valid()
    {
        $this->assertTrue($this->a->valid());
        $this->assertEquals('a', $this->a->current());
        $this->a->next();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('b', $this->a->current());
        $this->a->next();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('c', $this->a->current());
        $this->a->next();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('d', $this->a->current());
        $this->a->next();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('e', $this->a->current());
        $this->a->next();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('f', $this->a->current());
        $this->a->next();
        $this->assertFalse($this->a->valid());
        $this->a->rewind();
        $this->assertTrue($this->a->valid());
        $this->assertEquals('a', $this->a->current());
    }
    
    public function test_iterator()
    {
        foreach ($this->a as $i => $v) {
            $this->assertEquals($this->og[$i], $v);
        }
    }
    
    public function test_count()
    {
        $this->assertEquals(count($this->og), $this->a->count());
    }
    
}
?>
