<?php
/**
 * Unit tests for ActiveSession object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActiveSessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ActiveSession
     */
    protected $o;
    protected $aq;
    protected $session_id;
    protected $session_val;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        CREO('database', TestSetting::$mysql);
        
        CREO('mode', 'mysql');
        CREO('sessions_table', ActiveSession::$_table_name_);
        
        $this->drop_db_sql = "DROP DATABASE IF EXISTS `phpunit`;";
        $this->create_db_sql = "CREATE DATABASE `phpunit`;";
        $this->select_db_sql = "USE `phpunit`;";
        $this->drop_table_sql = "DROP TABLE IF EXISTS `".ActiveSession::$_table_name_."`;";
        
        $this->aq = new ActiveQuery(TestSetting::$mysql);
        $this->aq->query($this->drop_db_sql);
        $this->aq->query($this->create_db_sql);
        $this->aq->query($this->select_db_sql);
        
        ActiveSession::create_table();
        $this->o = new ActiveSession;
        
        $this->session_id = 'PHPUNIT'.time();
        $this->session_val = 'test data';
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->aq->query($this->drop_table_sql);
        $this->aq->query($this->drop_db_sql);
        unset($this->aq);
        unset($this->o);
    }

    public function testOpen()
    {
        $this->assertEquals('user', ini_get('session.save_handler'));
        $this->assertTrue($this->o->open());
    }

    public function testClose()
    {
        $this->assertTrue($this->o->open());
        $this->assertTrue($this->o->close());
    }

    public function testRead()
    {
        $this->assertTrue($this->o->open());
        $this->assertFalse($this->o->read(''));
        $this->assertTrue($this->o->close());
    }

    public function testWrite()
    {
        $this->assertTrue($this->o->open());
        $this->assertEquals('', $this->o->read($this->session_id));
        $this->assertEquals(1, $this->o->write($this->session_id, $this->session_val));
        $this->assertEquals($this->session_val, $this->o->read($this->session_id));
        $this->assertTrue($this->o->close());
    }

    public function testDestroy()
    {
        $this->assertTrue($this->o->open());
        $this->assertEquals('', $this->o->read($this->session_id));
        $this->assertEquals(1, $this->o->write($this->session_id, $this->session_val));
        $this->assertEquals(1, $this->o->destroy($this->session_id));
        $this->assertEquals('', $this->o->read($this->session_id));
        $this->assertTrue($this->o->close());
    }

    public function testGc()
    {
        ini_set('session.gc_maxlifetime', HOUR*-1);
        
        $this->assertTrue($this->o->open());
        $this->assertEquals('', $this->o->read($this->session_id));
        $this->assertEquals(1, $this->o->write($this->session_id, $this->session_val));
        $this->assertEquals($this->session_val, $this->o->read($this->session_id));
        
        $this->assertEquals(1, $this->o->gc(''));
        $this->assertEquals('', $this->o->read($this->session_id));
        $this->assertTrue($this->o->close());
    }

    public function testCreate_table()
    {
        // test done at setup
        $this->assertTrue(method_exists($this->o, 'create_table'));
    }

    public function testStart()
    {
        $this->assertTrue(method_exists($this->o, 'start'));
    }
}
?>
