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

class ActionViewTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var ActionView
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new ActionView;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
    }

    public function testProcess()
    {
        $f = CREOVEL_PATH . 'views' . DS . 'layouts' . DS . '_form_errors.php';
        $r = $this->o->process($f);
        $s = '<div class="errors">
<div class="top"></div>
<div class="body">
<ul>
</ul>
</div>
<div class="bottom"></div>
</div>'."\n";
        $this->assertEquals($s, $r);
        
        $r = $this->o->process($f, array('title' => 'TEST'));
        $s = '<div class="errors">
<div class="top"></div>
<div class="body">
<h1 class="error_title">TEST</h1><ul>
</ul>
</div>
<div class="bottom"></div>
</div>'."\n";
        $this->assertEquals($s, $r);
    }

    public function testTo_str()
    {
        $l = VIEWS_PATH . DS . 'layouts' . DS . 'default.html';
        $v = VIEWS_PATH . DS . 'index' . DS . 'index.html';
        
        $r = $this->o->to_str($v, $l);
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('layout' => false));
        $this->assertFalse(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('render' => false));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertFalse(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('render' => false, 'layout' => false));
        $this->assertFalse(CString::contains('<head>', $r));
        $this->assertFalse(CString::contains('<h1>Hello World!</h1>', $r));
        
        // render_text test
        $r = $this->o->to_str($v, $l, array('text' => 'TESTING'));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        $this->assertTrue(CString::contains('TESTING<h1>Hello World!</h1>', $r));
        
        // view <!--HEADSPLIT--> test
        $r = $this->o->to_str($v, $l, array('text' => '// comment in head<!--HEADSPLIT-->'));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        $this->assertTrue(CString::contains('// comment in head</head>', $r));
    }

    public function testShow()
    {
        // same as to_str but prints string out to screen.
        $this->assertTrue(method_exists($this->o, 'show'));
    }
}
?>
