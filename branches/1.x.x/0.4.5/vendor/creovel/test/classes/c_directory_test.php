<?php
/**
 * Unit tests for CDirectory object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CDirectoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CLocale
     */
    protected $d;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->d = new CDirectory;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test_ls()
    {
        $ls = $this->d->ls(dirname(__FILE__));
        $this->assertTrue(array_search(__FILE__, $ls) !== false);
    }
    
    public function test_ls_with_file_name()
    {
        $ls = $this->d->ls_with_file_name(dirname(__FILE__));
        $this->assertArrayHasKey('c_directory_test', $ls);
        $this->assertFalse(array_key_exists('test', $ls));
    }

}
