<?php
/**
 * Unit tests for MysqlImproved object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class MysqlImprovedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mysql
     */
    protected $a;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!extension_loaded('mysqli')) {
            $this->markTestSkipped(
              'The MySQLi extension is not available.'
            );
        }
        
        CREO('log_errors', true);
        CREO('log_queries', true);
        
        // GRANT ALL ON *.* TO 'phpunit'@'localhost' IDENTIFIED BY 'phpunit';
        
        $this->drop_db_sql = "DROP DATABASE IF EXISTS `phpunit`;";
        $this->create_db_sql = "CREATE DATABASE `phpunit`;";
        $this->select_db_sql = "USE `phpunit`;";
        $this->drop_table_sql = "DROP TABLE IF EXISTS `items`;";
        $this->create_table_sql = "CREATE TABLE  `phpunit`.`items` (
        `id` BIGINT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `title` VARCHAR( 100 ) NOT NULL ,
        `qty` SMALLINT( 3 ) UNSIGNED NOT NULL ,
        `desc` TEXT NOT NULL ,
        `created_at` DATETIME NOT NULL ,
        `updated_at` DATETIME NOT NULL
        ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
       
        $this->insert_row_sql = "INSERT INTO `phpunit`.`items` (`id`, `title`, `qty`, `desc`, `created_at`, `updated_at`) VALUES (NULL, 'iPhone', '100', 'Is there anything else?', NOW(), NOW());";
        
        $this->settings = array(
             'host'      => 'localhost',
             'username'  => 'phpunit',
             'password'  => 'phpunit'
             );

        $this->a = new MysqlImproved;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->a->connect($this->settings);
        $this->a->query($this->drop_db_sql);
        $this->a->disconnect();
        unset($this->a);
    }

    /**
     * Set connection and table for testing.
     *
     * @return void
     **/
    protected function connectAndCreateTable()
    {
        $this->a->connect($this->settings);
        $this->a->query($this->drop_db_sql);
        $this->a->query($this->create_db_sql);
        $this->a->query($this->select_db_sql);
        $this->a->query($this->drop_table_sql);
        $this->a->query($this->create_table_sql);
        $this->a->query($this->insert_row_sql); // add 1 row
        $this->a->query($this->insert_row_sql); // add 2 row
        $this->a->query($this->insert_row_sql); // add 3 row
        $this->a->query($this->insert_row_sql); // add 4 row
        $this->a->query($this->insert_row_sql); // add 5 row
        $this->a->query($this->insert_row_sql); // add 6 row
    }
    
    /**
     * Get an items row object by column id.
     *
     * @param integer $id
     * @return object
     * @author Nesbert Hidalgo
     **/
    public function getRowById($id)
    {
        $this->a->query(
            sprintf("SELECT * FROM `items` WHERE `id` = '%d';", $id)
            );
        return $this->a->current();
    }

    public function testConnect()
    {
        $this->a->connect($this->settings);
    }

    /**
     * @depends testConnect
     */
    public function testDisconnect()
    {
        $this->a->connect($this->settings);
        $this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testExecute()
    {
        $this->a->connect($this->settings);
        $this->a->execute('SHOW DATABASES;');
        $this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testQuery()
    {
        $this->a->connect($this->settings);
        $this->a->query('SHOW DATABASES;');
        $this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testClose()
    {
        $this->a->connect($this->settings);
        $this->a->query('SHOW DATABASES;');
        $this->a->disconnect();
    }
    
    /**
     * @depends testConnect
     */
    public function testGet_row()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $a = $this->a->get_row();
        $this->assertEquals(1, $a->id);
        $this->assertEquals('iPhone', $a->title);
    }

    /**
     * @depends testConnect
     */
    public function testColumns()
    {
        $this->connectAndCreateTable();
        $a = $this->a->columns('items');
        
        $keys = array_keys($a);
        $this->assertEquals('id', $keys[0]);
        $this->assertEquals('title', $keys[1]);
        $this->assertEquals('qty', $keys[2]);
        $this->assertEquals('desc', $keys[3]);
        $this->assertEquals('created_at', $keys[4]);
        $this->assertEquals('updated_at', $keys[5]);
    }

    /**
     * @depends testConnect
     */
    public function testTotal_rows()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        
        $this->assertEquals(6, $this->a->total_rows());
        
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        
        $this->a->query("SELECT * FROM `items`;");
        $this->assertEquals(9, $this->a->total_rows());
    }

    /**
     * @depends testConnect
     */
    public function testAffected_rows()
    {
        $this->connectAndCreateTable();
        $this->assertEquals(1, $this->a->affected_rows());
        
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        
        $this->assertEquals(1, $this->a->affected_rows());
        
        $this->a->query("UPDATE `items` SET `title` = 'iPhone 4';");
        
        $this->assertEquals(8, $this->a->affected_rows());
    }

    /**
     * @depends testConnect
     */
    public function testInsert_id()
    {
        $this->connectAndCreateTable();
        $this->assertEquals(6, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(7, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(8, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(9, $this->a->insert_id());
        
        $this->a->query("DELETE FROM `items` WHERE `id` = '4';");
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(10, $this->a->insert_id());
    }

    /**
     * @depends testConnect
     */
    public function testEscape()
    {
        $this->connectAndCreateTable();
        $t = "foo's bar";
        $this->assertEquals("foo\'s bar", $this->a->escape($t));
    }

    /**
     * @depends testConnect
     */
    public function testReset()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->a->reset();
        
        $this->assertEquals(0, $this->a->offset);
        $this->assertEquals('', $this->a->query);
        $this->assertFalse(is_resource($this->a->result));
    }

    /**
     * @depends testConnect
     */
    public function testFree_result()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->assertTrue(is_object($this->a->result));
        $this->a->free_result();
        $this->assertFalse(is_object($this->a->result));
    }

    /**
     * @depends testConnect
     */
    public function test_rewind()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->assertEquals(4, $this->a->offset);
        
        $this->a->rewind();
        $this->assertEquals(0, $this->a->offset);
    }
    
    /**
     * @depends testConnect
     */
    public function test_current()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $a = $this->a->current();
        $this->assertEquals(1, $a->id);
        
        $this->a->next();
        $this->a->next();
        
        $a = $this->a->current();
        $this->assertEquals(3, $a->id);
        
    }
    
    /**
     * @depends testConnect
     */
    public function test_key()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->a->next();
        $this->a->next();
        
        $this->assertEquals(2, $this->a->key());
        $this->a->next();
        
        $this->assertEquals(3, $this->a->key());
        
        $this->a->rewind();
        $this->assertEquals(0, $this->a->key());
    }
    
    /**
     * @depends testConnect
     */
    public function test_next()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->a->next();
        $this->a->next();
        
        $this->assertEquals(2, $this->a->offset);
        
        $this->a->next();
        $this->a->next();
        $this->assertEquals(4, $this->a->offset);
    }
    
    /**
     * @depends testConnect
     */
    public function testValid()
    {
        $this->assertFalse($this->a->valid());
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->assertTrue($this->a->valid());
    }
    
    /**
     * @depends testConnect
     */
    public function test_prev()
    {
        $this->connectAndCreateTable();
        $this->a->query("SELECT * FROM `items`;");
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->assertEquals(5, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(4, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(3, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(2, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(1, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(0, $this->a->offset);
    }
    
    /**
     * @depends testConnect
     */
    public function test_start_tran()
    {
        $this->connectAndCreateTable();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone', $a->title);
        
        $this->a->start_tran();
        $this->a->query("UPDATE `items` SET `title` = 'iPhone 4' WHERE `id` = '5';");
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone 4', $a->title);
        
        $this->a->rollback();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone', $a->title);
    }
    
    /**
     * @depends testConnect
     */
    public function test_rollback()
    {
        $this->connectAndCreateTable();
        
        $this->a->query("SELECT * FROM `items`;");
        $this->assertEquals(6, $this->a->total_rows());
        
        $this->a->start_tran();
        $this->a->query("DELETE FROM `items`;");
        $this->assertEquals(6, $this->a->affected_rows());
        
        $this->a->query("SELECT * FROM `items`;");
        $this->assertEquals(0, $this->a->total_rows());
        
        $this->a->rollback();
        
        $this->a->query("SELECT * FROM `items`;");
        $this->assertEquals(6, $this->a->total_rows());
    }
    
    /**
     * @depends testConnect
     */
    public function test_commit()
    {
        $this->connectAndCreateTable();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone', $a->title);
        
        $this->a->start_tran();
        $this->a->query("UPDATE `items` SET `title` = 'iPhone 4' WHERE `id` = '5';");
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone 4', $a->title);
        
        $this->a->commit();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->id);
        $this->assertEquals('iPhone 4', $a->title);
    }
    
}
?>
