<?php
/**
 * Unit tests for DatabaseFile object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class DatabaseFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DatabaseFile
     */
    protected $o;
    protected $a;
    protected $schema;
    protected $table;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!extension_loaded('mysql')) {
            $this->markTestSkipped(
              'The MySQL extension is not available.'
            );
        }
        
        $this->schema = 'test';
        $this->table = 'items';
        $this->select_db_sql = "USE `test`;";
        $this->drop_table_sql = "DROP TABLE IF EXISTS `items`;";
        $this->create_table_sql = "CREATE TABLE  `test`.`items` (
        `id` BIGINT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `title` VARCHAR( 100 ) NOT NULL ,
        `qty` SMALLINT( 3 ) UNSIGNED NOT NULL ,
        `desc` TEXT NOT NULL ,
        `created_at` DATETIME NOT NULL ,
        `updated_at` DATETIME NOT NULL
        ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
        
        $this->a = new Mysql(TestSetting::$mysql);
        $this->a->query($this->select_db_sql);
        $this->a->query($this->drop_table_sql);
        $this->a->query($this->create_table_sql);
        
        $this->o = new DatabaseFile;
        
        $this->ops = array(
            'class_name' => 'DatabaseFileTestItems',
            'file' => '/tmp/test.items.xml'
            );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
        
        $this->a->query($this->drop_table_sql);
        $this->a->disconnect();
        unset($this->a);
    }

    public function testCreate()
    {
        $file = DatabaseFile::create($this->ops);
        $this->assertTrue($file !== false);
        $this->assertEquals($file, $this->ops['file']);
        unlink($file);
    }

    public function testLoad()
    {
        $file = DatabaseFile::create($this->ops);
        
        $this->o->load($file);
        
        $this->assertEquals('SimpleXMLElement', get_class($this->o->xml));
        $this->assertTrue(is_array($this->o->columns()));
        $this->assertEquals(6, count($this->o->columns()));
        
        unlink($file);
    }

    public function testDefault_file()
    {
        $this->assertEquals(SCHEMAS_PATH . 'schema.table.xml', $this->o->default_file('schema', 'table'));
        $this->assertEquals(SCHEMAS_PATH . 'schema.test.table.xml', $this->o->default_file('schema', 'test_table'));
    }

    public function testColumns()
    {
        $file = DatabaseFile::create($this->ops);
        
        $this->o->load($file);
        
        $this->assertEquals('SimpleXMLElement', get_class($this->o->xml));
        $cols = $this->o->columns();
        
        $this->assertArrayHasKey('id', $cols);
        $this->assertArrayHasKey('title', $cols);
        $this->assertArrayHasKey('qty', $cols);
        $this->assertArrayHasKey('desc', $cols);
        $this->assertArrayHasKey('created_at', $cols);
        $this->assertArrayHasKey('updated_at', $cols);
        
        $this->assertEquals('ActiveRecordField', get_class($cols['id']));
        $this->assertEquals('ActiveRecordField', get_class($cols['title']));
        $this->assertEquals('ActiveRecordField', get_class($cols['qty']));
        $this->assertEquals('ActiveRecordField', get_class($cols['desc']));
        $this->assertEquals('ActiveRecordField', get_class($cols['created_at']));
        $this->assertEquals('ActiveRecordField', get_class($cols['updated_at']));
        
        
        $this->assertEquals('BIGINT', $cols['id']->type);
        $this->assertEquals('10', $cols['id']->size);
        $this->assertEquals('VARCHAR', $cols['title']->type);
        $this->assertEquals('100', $cols['title']->size);
        $this->assertEquals('SMALLINT', $cols['qty']->type);
        $this->assertEquals('3', $cols['qty']->size);
        $this->assertEquals('TEXT', $cols['desc']->type);
        $this->assertEquals('DATETIME', $cols['created_at']->type);
        $this->assertEquals('DATETIME', $cols['updated_at']->type);
        
        unlink($file);
    }
}

class DatabaseFileTestItems extends ActiveRecord
{
    public $_schema_ = 'test';
    public $_table_name_ = 'items';
    public function __construct($data, $c = null, $o = true)
    {
        parent::__construct($data, TestSetting::$mysql, $o);
    }   
}
?>
