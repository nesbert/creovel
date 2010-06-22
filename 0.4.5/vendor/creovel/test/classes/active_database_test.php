<?php
/**
 * Unit tests for ActiveDatabaseTest object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActiveDatabaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ActiveDatabase
     */
    protected $mysql;
    protected $mysqli;
    protected $ibm_db2;
    protected $mysql_create_table_sql;
    protected $mysql_drop_db_sql;
    protected $db2_create_table_sql;
    protected $db2_drop_db_sql;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (extension_loaded('mysql')) {
            $this->mysql = new ActiveDatabase;
        }
        if (extension_loaded('mysqli')) {
            $this->mysqli = new ActiveDatabase;
        }
        if (extension_loaded('ibm_db2')) {
            $this->ibm_db2 = new ActiveDatabase;
        }
        
        $this->mysql_create_table_sql =
            "CREATE TABLE creotest (
                id INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR( 100 ) NOT NULL,
                qty SMALLINT(3) NOT NULL
                );";
        $this->mysql_drop_table_sql = "DROP TABLE IF EXISTS creotest;";
        
        $this->db2_create_table_sql = 'CREATE TABLE CREOTEST (
            ID BIGINT NOT NULL GENERATED ALWAYS AS IDENTITY
                (START WITH 1, INCREMENT BY 1),
            TITLE VARCHAR (100) NOT NULL,
            QTY SMALLINT NOT NULL,
            PRIMARY KEY (ID)
            );';
        $this->db2_drop_table_sql = "DROP TABLE CREOTEST;";
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->ibm_db2);
        unset($this->mysqli);
        unset($this->mysql);
    }

    public function test__get()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue(empty($this->mysql->__database));
            $this->assertTrue(empty($this->mysql->__schema));
            $this->assertTrue(empty($this->mysql->__adapter));
            $this->assertTrue(empty($this->mysql->__adapter_obj));
            $this->mysql = new ActiveDatabase(TestSetting::$mysql);
            $this->assertEquals(TestSetting::$mysql['database'], $this->mysql->__database);
            $this->assertEquals(TestSetting::$mysql['database'], $this->mysql->__schema);
            $this->assertEquals('Mysql', $this->mysql->__adapter);
            $this->assertEquals('Mysql', (string) $this->mysql->__adapter_obj);
            $this->assertTrue(is_object($this->mysql->__adapter_obj));
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue(empty($this->mysqli->__database));
            $this->assertTrue(empty($this->mysqli->__schema));
            $this->assertTrue(empty($this->mysqli->__adapter));
            $this->assertTrue(empty($this->mysqli->__adapter_obj));
            $this->mysqli = new ActiveDatabase(TestSetting::$mysqli);
            $this->assertEquals(TestSetting::$mysqli['database'], $this->mysqli->__database);
            $this->assertEquals(TestSetting::$mysqli['database'], $this->mysqli->__schema);
            $this->assertEquals('MysqlImproved', $this->mysqli->__adapter);
            $this->assertEquals('MysqlImproved', (string) $this->mysqli->__adapter_obj);
            $this->assertTrue(is_object($this->mysqli->__adapter_obj));
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue(empty($this->ibm_db2->__database));
            $this->assertTrue(empty($this->ibm_db2->__schema));
            $this->assertTrue(empty($this->ibm_db2->__adapter));
            $this->assertTrue(empty($this->ibm_db2->__adapter_obj));
            $this->ibm_db2 = new ActiveDatabase(TestSetting::$db2);
            $this->assertEquals(TestSetting::$db2['database'], $this->ibm_db2->__database);
            $this->assertEquals(TestSetting::$db2['schema'], $this->ibm_db2->__schema);
            $this->assertEquals('IbmDb2', $this->ibm_db2->__adapter);
            $this->assertEquals('IbmDb2', (string) $this->ibm_db2->__adapter_obj);
            $this->assertTrue(is_object($this->ibm_db2->__adapter_obj));
        }
    }

    public function testConnection_properties()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertEquals(
                $GLOBALS['CREOVEL']['DATABASES'][strtoupper(CREO('mode'))],
                $this->mysql->connection_properties());
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertEquals(
                $GLOBALS['CREOVEL']['DATABASES'][strtoupper(CREO('mode'))],
                $this->mysqli->connection_properties());
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertEquals(
                $GLOBALS['CREOVEL']['DATABASES'][strtoupper(CREO('mode'))],
                $this->ibm_db2->connection_properties());
        }
    }

    public function testConnect()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue($this->mysql->connect(TestSetting::$mysql));
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue($this->mysqli->connect(TestSetting::$mysqli));
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue($this->ibm_db2->connect(TestSetting::$db2));
        }
    }

    public function testDisconnect()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue($this->mysql->connect(TestSetting::$mysql));
            $this->assertTrue($this->mysql->disconnect());
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue($this->mysqli->connect(TestSetting::$mysqli));
            $this->assertTrue($this->mysqli->disconnect());
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue($this->ibm_db2->connect(TestSetting::$db2));
            $this->assertTrue($this->ibm_db2->disconnect());
        }
    }

    public function testAdapter()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue($this->mysql->connect(TestSetting::$mysql));
            $this->assertEquals('Mysql', (string) $this->mysql->adapter());
            $this->assertTrue(is_object($this->mysql->adapter()));
            $this->assertTrue($this->mysql->disconnect());
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue($this->mysqli->connect(TestSetting::$mysqli));
            $this->assertEquals('MysqlImproved', (string) $this->mysqli->adapter());
            $this->assertTrue(is_object($this->mysqli->adapter()));
            $this->assertTrue($this->mysqli->disconnect());
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue($this->ibm_db2->connect(TestSetting::$db2));
            $this->assertEquals('IbmDb2', (string) $this->ibm_db2->adapter());
            $this->assertTrue(is_object($this->ibm_db2->adapter()));
            $this->assertTrue($this->ibm_db2->disconnect());
        }
    }

    public function testColumns()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue($this->mysql->connect(TestSetting::$mysql));
            $this->assertTrue($this->mysql->adapter()->query($this->mysql_drop_table_sql));
            $this->assertTrue($this->mysql->adapter()->query($this->mysql_create_table_sql));
                
            $cols = $this->mysql->columns('creotest');
            
            $this->assertTrue(is_array($cols));

            foreach ($cols as $col) {
                $this->assertTrue(is_object($col));
                $this->assertEquals('ActiveRecordField', (string) $col);
                $this->assertFalse(empty($col->type));
            }
            
            $this->assertTrue($this->mysql->adapter()->query($this->mysql_drop_table_sql));
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue($this->mysqli->connect(TestSetting::$mysqli));
            $this->assertTrue($this->mysqli->adapter()->query($this->mysql_drop_table_sql));
            $this->assertTrue($this->mysqli->adapter()->query($this->mysql_create_table_sql));
                
            $cols = $this->mysqli->columns('creotest');
            
            $this->assertTrue(is_array($cols));

            foreach ($cols as $col) {
                $this->assertTrue(is_object($col));
                $this->assertEquals('ActiveRecordField', (string) $col);
                $this->assertFalse(empty($col->type));
            }
            
            $this->assertTrue($this->mysqli->adapter()->query($this->mysql_drop_table_sql));
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue($this->ibm_db2->connect(TestSetting::$db2));
            $this->assertTrue(is_resource($this->ibm_db2->adapter()->query($this->db2_create_table_sql)));
                
            $cols = $this->ibm_db2->columns('CREOTEST');
            
            $this->assertTrue(is_array($cols));

            foreach ($cols as $col) {
                $this->assertTrue(is_object($col));
                $this->assertEquals('ActiveRecordField', (string) $col);
                $this->assertFalse(empty($col->type));
            }
            
            $this->assertTrue(is_resource($this->ibm_db2->adapter()->query($this->db2_drop_table_sql)));
        }
    }

    public function testGet_adapter_type()
    {
        // Mysql Adapter
        if (extension_loaded('mysql')) {
            $this->assertTrue($this->mysql->connect(TestSetting::$mysql));
            $this->assertEquals('mysql', $this->mysql->get_adapter_type());
            $this->assertTrue($this->mysql->disconnect());
        }
        // MysqlImproved Adapter
        if (extension_loaded('mysqli')) {
            $this->assertTrue($this->mysqli->connect(TestSetting::$mysqli));
            $this->assertEquals('mysql', $this->mysqli->get_adapter_type());
            $this->assertTrue($this->mysqli->disconnect());
        }
        // IbmDb2 Adapter
        if (extension_loaded('ibm_db2')) {
            $this->assertTrue($this->ibm_db2->connect(TestSetting::$db2));
            $this->assertEquals('db2', $this->ibm_db2->get_adapter_type());
            $this->assertTrue($this->ibm_db2->disconnect());
        }
    }
}
?>
