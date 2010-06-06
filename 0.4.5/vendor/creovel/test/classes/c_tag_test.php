<?php
/**
 * Unit tests for CString object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CTagTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    CTag
     * @access protected
     */
    protected $t;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->t = new CTag;
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

    public function testCreate()
    {
        $this->assertEquals('<p></p>', CTag::create('p'));
        $this->assertEquals('<p class="test"></p>',
            CTag::create('p', array('class' => 'test')));
        $this->assertEquals('<p class="test">hello world!</p>',
            CTag::create('p', array('class' => 'test'), 'hello world!'));
    }

    public function testAttributes()
    {
        $this->assertEquals('class="test" id="header"',
            CTag::attributes(array('class' => 'test', 'id' => 'header')));
        $this->assertEquals('<p class="test" id="header"></p>',
            CTag::create('p', array('class' => 'test', 'id' => 'header')));
    }

    public function testStylesheet_include()
    {
        $this->assertEquals('<link rel="stylesheet" type="text/css" media="screen" href="/path/to/file" />',
            CTag::stylesheet_include('/path/to/file'));
        $this->assertEquals('<link rel="stylesheet" type="text/css" media="print" href="/path/to/file" />',
            CTag::stylesheet_include('/path/to/file', 'print'));
    }

    public function testJavascript()
    {
        $this->assertEquals('<script type="text/javascript">alert("hello world!");</script>',
            CTag::javascript('alert("hello world!");'));
    }

    public function testJavascript_include()
    {
        $this->assertEquals('<script src="/path/to/file" type="text/javascript"></script>',
            CTag::javascript_include('/path/to/file'));
        $this->assertEquals('<script id="foo" src="/path/to/file" type="text/javascript"></script>',
            CTag::javascript_include('/path/to/file', array('id' => 'foo')));
    }

    public function testLink_to()
    {
        $this->assertEquals(
            '<a href="user/profile/show">Profile</a>',
            CTag::link_to('Profile', 'user', 'profile', 'show'));
        $this->assertEquals(
            '<a class="menu-link" href="user/profile/show">Profile</a>',
            CTag::link_to('Profile', 'user', 'profile', 'show',
                array('class' => 'menu-link')));
    }

    public function testLink_to_url()
    {
        $this->assertEquals(
            '<a href="http://google.com">Google</a>',
            CTag::link_to_url('Google', 'http://google.com'));
        $this->assertEquals(
            '<a class="best-in" href="http://apple.com">Apple</a>',
            CTag::link_to_url('Apple', 'http://apple.com', array('class' => 'best-in')));
    }

    public function testLink_to_google_maps()
    {
        $this->assertEquals(
            '<a title="Apple" href="http://maps.google.com/maps?q=1+Infinite+Loop+Cupertino+CA+95014+(Apple)">Directions to Apple HQ</a>',
            CTag::link_to_google_maps(
                'Directions to Apple HQ',
                '1 Infinite Loop Cupertino, CA 95014',
                array('title' => 'Apple')));
    }

    public function testMail_to()
    {
        $this->assertEquals(
            '<a href="mailto:test@test.com">test@test.com</a>',
            CTag::mail_to('test@test.com'));
        $this->assertEquals(
            '<a href="mailto:test@test.com">Full Name</a>',
            CTag::mail_to('test@test.com', 'Full Name'));
    }
}
?>
