<?php
/**
 * Unit tests for ActionController object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActionControllerTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var ActionController
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new IndexController;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {}

    public function test__set_events()
    {
        $events = array(
            'controller' => 'blog',
            'action' => 'new-post',
            'layout' => 'fresh',
            'nested_controller_path' => 'admin/user',
            );
            
         $this->o->__set_events($events);
         $this->assertEquals($events['controller'], $this->o->_controller);
         $this->assertEquals(Inflector::underscore($events['action']), $this->o->_action);
         $this->assertEquals(Inflector::underscore($events['action']), $this->o->render);
         $this->assertEquals($events['layout'], $this->o->layout);
         $this->assertEquals($events['nested_controller_path'], $this->o->_nested_controller_path);
    }

    public function test__set_params()
    {
        $params = array('id' => 'test');
        $this->o->__set_params($params);
        $this->assertEquals($params, $this->o->params);
    }

    public function test__execute_action()
    {
        $events = array('controller' => 'index', 'action' => 'index');
        $this->assertEquals(0, $this->o->count);
        $this->o->__set_events($events);
        $this->o->__execute_action();
        $this->assertEquals(5, $this->o->count);
    }

    public function testInitialize()
    {
        // callback
        $this->assertTrue(method_exists('ActionController', 'initialize'));
    }

    public function testBefore_filter()
    {
        // callback
        $this->assertTrue(method_exists('ActionController', 'before_filter'));
    }

    public function testAfter_filter()
    {
        // callback
        $this->assertTrue(method_exists('ActionController', 'after_filter'));
    }

    public function test__output()
    {
        $events = array('controller' => 'index', 'action' => 'index');
        $this->assertEquals(0, $this->o->count);
        $this->o->__set_events($events);
        $this->o->__execute_action();
        $this->assertEquals(5, $this->o->count);
        
        $this->_controller = 'index';
        $this->_action = 'index';
        $this->layout = 'default';
                
        $l = VIEWS_PATH . DS . 'layouts' . DS . 'default.html';
        $v = VIEWS_PATH . DS . 'index' . DS . 'index.html';
        $r = ActionView::to_str($v, $l);
        
        $this->assertEquals($r, $this->o->__output(true));
    }

    public function testBuild_partial()
    {
        $this->expectOutputString('<h2>General Error Page</h2>

<p>The following error occurred: <em>TESTING</em></p>

<h2>How to Edit This Page...</h2>

<p>To change this custom error page, update <em>'.VIEWS_PATH.'index/index.html</em></p>
');
        
        $this->o->__set_events(array('controller' => 'index', 'action' => 'index'));
        $this->o->__set_params(array('error' => 'TESTING'));
        $this->o->build_partial('general', array('test' => 'testing'), 'errors');
    }

    public function testBuild_partial_to_str()
    {
        $s = '<h2>General Error Page</h2>

<p>The following error occurred: <em>TESTING</em></p>

<h2>How to Edit This Page...</h2>

<p>To change this custom error page, update <em>'.VIEWS_PATH.'index/index.html</em></p>
';
        
        $this->o->__set_events(array('controller' => 'index', 'action' => 'index'));
        $this->o->__set_params(array('error' => 'TESTING'));
        $this->assertEquals(
            $s,
            $this->o->build_partial_to_str('general', array('test' => 'testing'), 'errors')
            );
    }
    
    public function testRender_partial()
    {
        // same test as build_partial but prepends a "_" to the file name
        $this->assertTrue(method_exists('ActionController', 'render_partial'));
    }

    public function testRender_partial_to_str()
    {
        // same test as build_partial_to_str but prepends a "_" to the file name
        $this->assertTrue(method_exists('ActionController', 'render_partial_to_str'));
    }

    public function testBuild_controller()
    {
        $this->params = array('error' => 'TESTING');
        $this->_controller = 'errors';
        $this->_action = 'general';
        
        $l = VIEWS_PATH . DS . 'layouts' . DS . 'default.html';
        $v = VIEWS_PATH . DS . 'errors' . DS . 'general.html';
        $r = ActionView::to_str($v, $l);
        
        $this->expectOutputString($r);
        $this->o->test_build_controller();
    }

    public function testBuild_controller_to_str()
    {
        $this->params = array('error' => 'TESTING');
        $this->_controller = 'errors';
        $this->_action = 'general';
        
        $l = VIEWS_PATH . DS . 'layouts' . DS . 'default.html';
        $v = VIEWS_PATH . DS . 'errors' . DS . 'general.html';
        $r = ActionView::to_str($v, $l);
        
        $this->assertEquals($r, $this->o->test_build_controller(1));
    }

    public function testRun()
    {
        $this->assertEquals(0, $this->o->count);
        $this->o->run('index');
        $this->assertEquals('index', $this->o->render);
        $this->assertEquals(1, $this->o->count);
    }

    public function testNo_view()
    {
        $this->o->no_view();
        $this->assertFalse($this->o->layout);
        $this->assertFalse($this->o->render);
    }

    public function testRender_text()
    {
        $s = 'Testing';
        $this->o->render_text($s);
        $this->assertEquals($s, $this->o->render_text);
        $this->o->render_text($s, true);
        $this->assertEquals($s.$s, $this->o->render_text);
        $this->o->render_text($s);
        $this->assertEquals($s, $this->o->render_text);
    }

    public function testIs_posted()
    {
        $this->assertFalse($this->o->is_posted());
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->o->is_posted());
    }

    public function testThrow_error()
    {
        $this->assertTrue(method_exists('ActionController', 'throw_error'));
    }
}

class IndexController extends ApplicationController
{
    public $count = 0;
    public function index() { $this->count++; }
    public function initialize() { $this->index(); }
    public function initialize_index_controller() { $this->index(); }
    public function before_filter() { $this->index(); }
    public function after_filter() { $this->index(); }
    public function set_env()
    {
        $_SERVER['REQUEST_URI'] = 'http://www.testapp.com/news/article/id/12345';
        $GLOBALS['CREOVEL']['DISPATCHER'] = 'index.php';
        
        // Set routing defaults
        $GLOBALS['CREOVEL']['ROUTING'] = @parse_url($_SERVER['REQUEST_URI']);
        $GLOBALS['CREOVEL']['ROUTING']['current'] = array();
        $GLOBALS['CREOVEL']['ROUTING']['routes'] = array();
        
        // set additional routing options
        $GLOBALS['CREOVEL']['ROUTING']['base_path'] = Creovel::base_path();
        $GLOBALS['CREOVEL']['ROUTING']['base_url'] = '/';
    }
    public function test_build_controller($to_str = false)
    {
        $this->set_env();
        if (empty($to_str)) {
            $this->build_controller('errors', 'general', null, array('error' => 'TESTING'));
        } else {
            return $this->build_controller_to_str('errors', 'general', null, array('error' => 'TESTING'));
        }
    }
}
?>
