<?php
/**
 * Unit tests for Cipher object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CipherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Cipher
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped(
              'The Mcrypt extension is not available.'
            );
        }
        
        $this->o = new Cipher;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
    }
    
    protected function all($s, $l = 1, $k = null)
    {
        $e = $this->o->encrypt($s, $l, $k);
        $this->assertEquals($e, Cipher::encrypt($s, $l, $k));
        
        $d = $this->o->decrypt($e, $l, $k);
        $this->assertEquals($s, $d);
        $this->assertEquals($d, Cipher::decrypt($e, $l, $k));
        $this->assertEquals($s, Cipher::decrypt($e, $l, $k));
        
    }

    public function testEncrypt()
    {
        $s = 'www.creovel.org';
        $e = $this->o->encrypt($s);
        $this->assertEquals($e, Cipher::encrypt($s));
    }

    public function testDecrypt()
    {
        $s = 'www.creovel.org';
        $this->all($s);
        
        // level tests
        $this->all($s, 2);
        $this->all($s, 3);
        $this->all($s, 4);
        $this->all($s, 5); // will default to 1
        
        // level tests with key/salt
        $k = 'keepitDRY';
        $this->all($s, 1, $k);
        $this->all($s, 2, $k);
        $this->all($s, 3, $k);
        $this->all($s, 4, $k);
    }
}
?>
