<?php
/**
 * Unit tests for Paginator object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class PaginatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Paginator
     */
    protected $obj;
    protected $arr;
    protected $num;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // object
        $this->o = new Paginator;
        // array
        $this->a = new Paginator;
        // numeric
        $this->n = new Paginator;
        
        $this->params = array('city' => 'LA', 'sort' => 'ASC');
        
        $this->o_data = (object) array(
            'total_records' => 1000
            );
        
        $this->a_data = array();
        for ($i = 0; $i < 1000; $i++) {
            $this->a_data[] = "Record ". ($i + 1);
        }
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
        unset($this->a);
        unset($this->n);
    }

    public function testSet_properties()
    {
        $this->o->set_properties($this->o_data);
        $this->assertEquals(1000,   $this->o->total_records);
        $this->assertEquals(100,    $this->o->total_pages);
        $this->assertEquals(1,      $this->o->current);
        $this->assertEquals(2,      $this->o->next);
        $this->assertEquals(1,      $this->o->prev);
        $this->assertEquals(1,      $this->o->first);
        $this->assertEquals(100,    $this->o->last);
        $this->assertEquals(10,     $this->o->limit);
        $this->assertEquals(1,      $this->o->current_min);
        $this->assertEquals(10,     $this->o->current_max);
        
        $this->a->set_properties($this->a_data);
        $this->assertEquals(1000,   $this->a->total_records);
        $this->assertEquals(100,    $this->a->total_pages);
        $this->assertEquals(1,      $this->a->current);
        $this->assertEquals(2,      $this->a->next);
        $this->assertEquals(1,      $this->a->prev);
        $this->assertEquals(1,      $this->a->first);
        $this->assertEquals(100,    $this->a->last);
        $this->assertEquals(10,     $this->a->limit);
        $this->assertEquals(1,      $this->a->current_min);
        $this->assertEquals(10,     $this->a->current_max);
                
        $this->n->set_properties(1000);
        $this->assertEquals(1000,   $this->n->total_records);
        $this->assertEquals(100,    $this->n->total_pages);
        $this->assertEquals(1,      $this->n->current);
        $this->assertEquals(2,      $this->n->next);
        $this->assertEquals(1,      $this->n->prev);
        $this->assertEquals(1,      $this->n->first);
        $this->assertEquals(100,    $this->n->last);
        $this->assertEquals(10,     $this->n->limit);
        $this->assertEquals(1,      $this->n->current_min);
        $this->assertEquals(10,     $this->n->current_max);
    }

    public function testPage_array()
    {
        $this->a->set_properties($this->a_data);
        $a = $this->a->page_array($this->a_data);
        $this->assertEquals(10, count($a));
        $this->assertEquals('Record 1', reset($a));
        $this->assertEquals('Record 2', next($a));
        $this->assertEquals('Record 3', next($a));
        $this->assertEquals('Record 4', next($a));
        $this->assertEquals('Record 5', next($a));
        $this->assertEquals('Record 6', next($a));
        $this->assertEquals('Record 7', next($a));
        $this->assertEquals('Record 8', next($a));
        $this->assertEquals('Record 9', next($a));
        $this->assertEquals('Record 10', next($a));
        $this->assertEquals('Record 1', reset($a));
        $this->assertEquals('Record 10', end($a));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $a = $this->a->page_array($this->a_data);
        $this->assertEquals(20, count($a));
        $this->assertEquals('Record 81', reset($a));
        $this->assertEquals('Record 82', next($a));
        $this->assertEquals('Record 83', next($a));
        $this->assertEquals('Record 84', next($a));
        $this->assertEquals('Record 85', next($a));
        $this->assertEquals('Record 86', next($a));
        $this->assertEquals('Record 87', next($a));
        $this->assertEquals('Record 88', next($a));
        $this->assertEquals('Record 89', next($a));
        $this->assertEquals('Record 90', next($a));
        $this->assertEquals('Record 91', next($a));
        $this->assertEquals('Record 92', next($a));
        $this->assertEquals('Record 93', next($a));
        $this->assertEquals('Record 94', next($a));
        $this->assertEquals('Record 95', next($a));
        $this->assertEquals('Record 96', next($a));
        $this->assertEquals('Record 97', next($a));
        $this->assertEquals('Record 98', next($a));
        $this->assertEquals('Record 99', next($a));
        $this->assertEquals('Record 100', next($a));
        $this->assertEquals('Record 81', reset($a));
        $this->assertEquals('Record 100', end($a));
    }

    public function testParams_to_str()
    {
        $params_str = '&city=LA&sort=ASC';
        
        $this->assertEquals($params_str, $this->o->params_to_str($this->params));
        $this->assertEquals($params_str, $this->a->params_to_str($this->params));
        $this->assertEquals($params_str, $this->n->params_to_str($this->params));
    }

    public function testLink_to_next()
    {
        $expected_str = '<a href="?page=6&city=LA&sort=ASC">Next</a>';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->link_to_next(null, $this->params));
        $this->assertEquals(str_replace('Next', '>', $expected_str), $this->o->link_to_next('>', $this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->link_to_next(null, $this->params));
        $this->assertEquals(str_replace('Next', '>', $expected_str), $this->a->link_to_next('>', $this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->link_to_next(null, $this->params));
        $this->assertEquals(str_replace('Next', '>', $expected_str), $this->n->link_to_next('>', $this->params));
    }

    public function testLink_to_prev()
    {
        $expected_str = '<a href="?page=4&city=LA&sort=ASC">Prev</a>';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->link_to_prev(null, $this->params));
        $this->assertEquals(str_replace('Prev', '<', $expected_str), $this->o->link_to_prev('<', $this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->link_to_prev(null, $this->params));
        $this->assertEquals(str_replace('Prev', '<', $expected_str), $this->a->link_to_prev('<', $this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->link_to_prev(null, $this->params));
        $this->assertEquals(str_replace('Prev', '<', $expected_str), $this->n->link_to_prev('<', $this->params));
    }

    public function testLink_to_first()
    {
        $expected_str = '<a href="?page=1&city=LA&sort=ASC">First</a>';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->link_to_first(null, $this->params));
        $this->assertEquals(str_replace('First', '<<', $expected_str), $this->o->link_to_first('<<', $this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->link_to_first(null, $this->params));
        $this->assertEquals(str_replace('First', '<<', $expected_str), $this->a->link_to_first('<<', $this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->link_to_first(null, $this->params));
        $this->assertEquals(str_replace('First', '<<', $expected_str), $this->n->link_to_first('<<', $this->params));
    }

    public function testLink_to_last()
    {
        $expected_str = '<a href="?page=50&city=LA&sort=ASC">Last</a>';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->link_to_last(null, $this->params));
        $this->assertEquals(str_replace('Last', '>>', $expected_str), $this->o->link_to_last('>>', $this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->link_to_last(null, $this->params));
        $this->assertEquals(str_replace('Last', '>>', $expected_str), $this->a->link_to_last('>>', $this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->link_to_last(null, $this->params));
        $this->assertEquals(str_replace('Last', '>>', $expected_str), $this->n->link_to_last('>>', $this->params));
    }

    public function testPaging_links()
    {
        $expected_str = '<div class="page-links"><a class="prev" href="?page=4&city=LA&sort=ASC">&laquo; Prev</a><a class="page-1" href="?page=1&city=LA&sort=ASC">1</a><span class="dots">...</span><a class="page-3" href="?page=3&city=LA&sort=ASC">3</a><a class="page-4" href="?page=4&city=LA&sort=ASC">4</a><a class="page-5 current">5</a><a class="page-6" href="?page=6&city=LA&sort=ASC">6</a><a class="page-7" href="?page=7&city=LA&sort=ASC">7</a><span class="dots">...</span><a class="page-50" href="?page=50&city=LA&sort=ASC">50</a><a class="next" href="?page=6&city=LA&sort=ASC">Next &raquo;</a></div>';
        $expected_str2 = str_replace('<div class="page-links">', '<div class="page-links"><span class="page-label">Page 5 of 50</span>', $expected_str);
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->paging_links($this->params));
        $this->assertEquals($expected_str2, $this->o->paging_links($this->params, true));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->paging_links($this->params));
        $this->assertEquals($expected_str2, $this->a->paging_links($this->params, true));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->paging_links($this->params));
        $this->assertEquals($expected_str2, $this->n->paging_links($this->params, true));
    }

    public function testPaging_limit()
    {
        $expected_str = '<select onchange="location.href=this.options[this.selectedIndex].value">
<option value="?page=5&limit=10&city=LA&sort=ASC">10</option>
<option value="?page=5&limit=20&city=LA&sort=ASC" selected="selected">20</option>
<option value="?page=5&limit=50&city=LA&sort=ASC">50</option>
<option value="?page=5&limit=100&city=LA&sort=ASC">100</option>
</select>
';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->paging_limit($this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->paging_limit($this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->paging_limit($this->params));
    }

    public function testPaging_label()
    {
        $expected_str = '<span class="page-label">Page 5 of 50</span>';
        $expected_str2 = '<span class="page-label">Page 10 of 100</span>';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->paging_label());
        $this->o->set_properties($this->o_data, 10);
        $this->assertEquals($expected_str2, $this->o->paging_label());
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->paging_label());
        $this->a->set_properties($this->a_data, 10);
        $this->assertEquals($expected_str2, $this->a->paging_label());
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->paging_label());
        $this->n->set_properties(1000, 10);
        $this->assertEquals($expected_str2, $this->n->paging_label());
    }

    public function testTotal_records()
    {
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals(1000, $this->o->total_records());
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals(1000, $this->a->total_records());
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals(1000, $this->n->total_records());
    }

    public function testTotal_pages()
    {
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals(50, $this->o->total_pages());
        $this->o->set_properties($this->o_data, 5);
        $this->assertEquals(100, $this->o->total_pages());
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals(50, $this->a->total_pages());
        $this->a->set_properties($this->a_data, 5);
        $this->assertEquals(100, $this->a->total_pages());
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals(50, $this->n->total_pages());
        $this->n->set_properties(1000, 5);
        $this->assertEquals(100, $this->n->total_pages());
    }

    public function testNeeds_links()
    {
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertTrue($this->o->needs_links());
        $this->o_data->total_records = 55;
        $this->o->set_properties($this->o_data, 1, 100);
        $this->assertFalse($this->o->needs_links());
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertTrue($this->a->needs_links());
        $this->a->set_properties(array_slice($this->a_data, 0, 54), 1, 100);
        $this->assertFalse($this->a->needs_links());
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertTrue($this->n->needs_links());
        $this->n->set_properties(55, 1, 100);
        $this->assertFalse($this->n->needs_links());
    }

    public function testPaging_link()
    {
        $expected_str = '?page=3&city=LA&sort=ASC';
        
        $this->o->set_properties($this->o_data, 5, 20);
        $this->assertEquals($expected_str, $this->o->paging_link(3, $this->params));
        
        $this->a->set_properties($this->a_data, 5, 20);
        $this->assertEquals($expected_str, $this->a->paging_link(3, $this->params));
        
        $this->n->set_properties(1000, 5, 20);
        $this->assertEquals($expected_str, $this->n->paging_link(3, $this->params));
    }
}
?>
