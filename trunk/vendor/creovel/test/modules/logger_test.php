<?php
/**
 * Unit tests for Logger object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class LoggerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new Logger;
        $this->path = '/tmp/';
        $this->filename = 'creovel.logger.test.log';
        $this->file = $this->path . $this->filename;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $files = CDirectory::ls($this->path, array('filter' => '/^'.$this->filename.'/'));
        foreach ($files as $file) {
            unlink($file);
        }
        
        unset($this->o);
    }

    public function testWrite()
    {
        $p = 'Testing';
        $s = str_repeat($p, 250);
        $this->assertFalse($this->o->write($s));
        
        // set logger vars
        $this->o->filename = $this->file;
        $this->o->timestamp = false; // disable timestamp
        
        // 4 x 250 = 1000
        $this->assertTrue($this->o->write($s)); // 1
        
        $c = file_get_contents($this->file);
        $this->assertTrue(strstr($c, date('Y-m-d')) === false);
        
        $this->o->timestamp = true; // enable timestamp
        
        $this->assertTrue($this->o->write($s)); // 2
        $this->assertTrue($this->o->write($s)); // 3
        $this->assertTrue($this->o->write($s)); // 4
        
        $c = file_get_contents($this->file);
        
        $this->assertTrue(strstr($c, $p) !== false);
        $this->assertTrue(strstr($c, date('Y-m-d')) !== false);
        
        preg_match_all("/{$p}/", $c, $matches, PREG_OFFSET_CAPTURE);
        
        $this->assertEquals(1000, count($matches[0]));
    }

    public function testPartition()
    {
        $this->o->filename = $this->file;
        $this->o->filesize_limit = 1000;
        
        $s = str_repeat('11', 4);
        
        for ($i = 0; $i < 10000; $i++) {
            $this->assertTrue($this->o->write($s));
        }
        
        $ls = CDirectory::ls($this->path, array('filter' => '/^'.$this->filename.'/'));
        $this->assertTrue(count($ls) >= 4);
    }
}
?>
