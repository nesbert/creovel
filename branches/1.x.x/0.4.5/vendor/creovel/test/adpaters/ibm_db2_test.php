<?php
/**
 * Unit tests for IbmDb2 object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class IbmDb2Test extends PHPUnit_Framework_TestCase
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
        if (!extension_loaded('ibm_db2')) {
            $this->markTestSkipped(
              'The IBM DB2 (ibm_db2) extension is not available.'
            );
        }
        
        CREO('log_errors', true);
        CREO('log_queries', true);
        
        $this->settings = array(
             'host'      => 'localhost',
             'username'  => 'db2inst1',
             'password'  => 'password',
             'database'  => '', // enter database
             'schema'    => '', // enter schema
             );
        
        $this->drop_table_sql = 'DROP TABLE ITEMS;';
        $this->create_table_sql = 'CREATE TABLE ITEMS (
            ID BIGINT NOT NULL GENERATED ALWAYS AS IDENTITY
                (START WITH 1, INCREMENT BY 1),
            TITLE VARCHAR (100) NOT NULL,
            QTY SMALLINT NOT NULL,
            DESC VARCHAR (4096) NOT NULL,
            CREATED_AT TIMESTAMP NOT NULL,
            UPDATED_AT TIMESTAMP NOT NULL,
            PRIMARY KEY (ID)
            );';
       
        $this->insert_row_sql = 'INSERT INTO ITEMS
            (ID, TITLE, QTY, DESC, CREATED_AT, UPDATED_AT) VALUES' ."
            (DEFAULT, 'iPhone', '100', 'Is there anything else?', CURRENT TIMESTAMP, CURRENT TIMESTAMP);";
        $this->test_sql = 'SELECT TABSCHEMA, TABNAME, COLNO, COLNAME, TYPENAME, LENGTH, DEFAULT, IDENTITY, GENERATED " .
             "FROM SYSCAT.COLUMNS;';
        $this->a = new IbmDb2;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->a);
    }
    
    /**
     * Initialize DB for regular test by connecting and creating
     * items table and some records.
     */
    protected function startDB2()
    {
        $this->a->connect($this->settings);
        $this->a->query($this->create_table_sql);
        $this->createRows();
    }
    
    /**
     * Clean up DB for regular test by disconnecting and dropping
     * items table.
     */
    protected function endDB2()
    {
        $this->a->query($this->drop_table_sql);
        $this->a->disconnect();
    }
    
    /**
     * Create dummy rows.
     */
    protected function createRows()
    {
        $this->a->query($this->insert_row_sql); // add 1 row
        $this->a->query($this->insert_row_sql); // add 2 row
        $this->a->query($this->insert_row_sql); // add 3 row
        $this->a->query($this->insert_row_sql); // add 4 row
        $this->a->query($this->insert_row_sql); // add 5 row
        $this->a->query($this->insert_row_sql); // add 6 row
    }
    
    /**
     * Get an ITEMS row object by column ITEMS.ID.
     *
     * @param integer $id
     * @return object
     * @author Nesbert Hidalgo
     **/
    public function getRowById($id)
    {
        $this->a->query(
            sprintf("SELECT * FROM ITEMS WHERE ID = '%d';", $id)
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
        $this->a->execute($this->test_sql);
        $this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testQuery()
    {
        $this->a->connect($this->settings);
        $this->a->query($this->test_sql);
    	$this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testClose()
    {
        $this->a->connect($this->settings);
        $this->a->query($this->test_sql);
        $this->a->close();
        $this->a->disconnect();
    }
    
    /**
     * @depends testConnect
     */
    public function testGet_row()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $a = $this->a->get_row();
        $this->assertEquals(1, $a->ID);
        $this->assertEquals('iPhone', $a->TITLE);
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testColumns()
    {
        $this->startDB2();
        
        $a = $this->a->columns('ITEMS');
        
        $keys = array_keys($a);
        $this->assertEquals('ID', $keys[0]);
        $this->assertEquals('TITLE', $keys[1]);
        $this->assertEquals('QTY', $keys[2]);
        $this->assertEquals('DESC', $keys[3]);
        $this->assertEquals('CREATED_AT', $keys[4]);
        $this->assertEquals('UPDATED_AT', $keys[5]);
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testTotal_rows()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        
        $this->assertEquals(6, $this->a->total_rows());
        
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertEquals(9, $this->a->total_rows());
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testAffected_rows()
    {
        $this->startDB2();
        
        $this->assertEquals(1, $this->a->affected_rows());
        
        $this->a->query($this->insert_row_sql);
        $this->a->query($this->insert_row_sql);
        
        $this->assertEquals(1, $this->a->affected_rows());
        
        $this->a->query("UPDATE ITEMS SET ITEMS.TITLE = 'iPhone 4';");
        
        $this->assertEquals(8, $this->a->affected_rows());
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testInsert_id()
    {
        $this->startDB2();
        
        $this->assertEquals(6, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(7, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(8, $this->a->insert_id());
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(9, $this->a->insert_id());
        
        $this->a->query("DELETE FROM ITEMS WHERE ITEMS.ID = '4';");
        
        $this->a->query($this->insert_row_sql);
        $this->assertEquals(10, $this->a->insert_id());
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testEscape()
    {
        $this->a->connect($this->settings);
        $t = "foo's bar";
        $this->assertEquals("foo''s bar", $this->a->escape($t));
        $this->a->disconnect();
    }

    /**
     * @depends testConnect
     */
    public function testReset()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->a->reset();
        
        $this->assertEquals(1, $this->a->offset);
        $this->assertEquals('', $this->a->query);
        $this->assertFalse(is_resource($this->a->result));
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function testFree_result()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertTrue(is_resource($this->a->result));
        $this->a->free_result();
        $this->assertFalse(is_resource($this->a->result));
        
        $this->endDB2();
    }

    /**
     * @depends testConnect
     */
    public function test_rewind()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->assertEquals(5, $this->a->offset);
        
        $this->a->rewind();
        $this->assertEquals(1, $this->a->offset);
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_current()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $a = $this->a->current();
        $this->assertEquals(1, $a->ID);
        
        $this->a->next();
        $this->a->next();
        
        $a = $this->a->current();
        $this->assertEquals(3, $a->ID);
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_key()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->a->next();
        $this->a->next();
        
        $this->assertEquals(3, $this->a->key());
        $this->a->next();
        
        $this->assertEquals(4, $this->a->key());
        
        $this->a->rewind();
        $this->assertEquals(1, $this->a->key());
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_next()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->a->next();
        $this->a->next();
        
        $this->assertEquals(3, $this->a->offset);
        
        $this->a->next();
        $this->a->next();
        $this->assertEquals(5, $this->a->offset);
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function testValid()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertTrue($this->a->valid());
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_prev()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->a->next();
        $this->assertEquals(6, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(5, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(4, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(3, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(2, $this->a->offset);
        $this->a->prev();
        $this->assertEquals(1, $this->a->offset);
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_start_tran()
    {
        $this->startDB2();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone', $a->TITLE);
        
        $this->a->start_tran();
        $this->a->query("UPDATE ITEMS SET ITEMS.TITLE = 'iPhone 4' WHERE ITEMS.ID = '5';");
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone 4', $a->TITLE);
        
        $this->a->rollback();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone', $a->TITLE);
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_rollback()
    {
        $this->startDB2();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertEquals(6, $this->a->total_rows());
        
        $this->a->start_tran();
        
        $this->a->query("DELETE FROM ITEMS;");
        $this->assertEquals(6, $this->a->affected_rows());
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertEquals(0, $this->a->total_rows());
        
        $this->a->rollback();
        
        $this->a->query("SELECT * FROM ITEMS;");
        $this->assertEquals(6, $this->a->total_rows());
        
        $this->endDB2();
    }
    
    /**
     * @depends testConnect
     */
    public function test_commit()
    {
        $this->startDB2();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone', $a->TITLE);
        
        $this->a->start_tran();
        $this->a->query("UPDATE ITEMS SET ITEMS.TITLE = 'iPhone 4' WHERE ITEMS.ID = '5';");
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone 4', $a->TITLE);
        
        $this->a->commit();
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone 4', $a->TITLE);
        
        // check that you can autocommit after trans
        $this->a->query("UPDATE ITEMS SET ITEMS.TITLE = 'iPhone 3GS' WHERE ITEMS.ID = '5';");
        
        $a = $this->getRowById(5);
        $this->assertEquals(5, $a->ID);
        $this->assertEquals('iPhone 3GS', $a->TITLE);
        
        $this->endDB2();
    }
    
}
?>
