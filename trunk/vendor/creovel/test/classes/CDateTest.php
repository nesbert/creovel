<?php
/**
 * Unit tests for CDate object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CDateTest extends PHPUnit_Framework_TestCase
{
    public function testDatetime()
    {
        $now = date('Y-m-d H:i:s');
        $tomorrow = date('Y-m-d H:i:s', strtotime('+1day'));
        $yesterday = date('Y-m-d H:i:s', strtotime('-1day'));
        $datetime = array();
        $datetime['hour'] = 12;
        $datetime['minute'] = 55;
        $datetime['second'] = 23;
        $datetime['month'] = 3;
        $datetime['day'] = 6;
        $datetime['year'] = 2010;
        
        $this->assertEquals($now, CDate::datetime());
        $this->assertEquals($tomorrow, CDate::datetime(time() + DAY));
        $this->assertEquals($yesterday, CDate::datetime(time() - DAY));
        $this->assertEquals($tomorrow, CDate::datetime(strtotime('+1day')));
        $this->assertEquals($yesterday, CDate::datetime(strtotime('-1day')));
        $this->assertEquals('2010-03-06 12:55:23', CDate::datetime($datetime));
    }

    public function testGmtime()
    {
        $now = strtotime(gmdate('Y-m-d H:i:s'));
        $this->assertEquals($now, CDate::gmtime());
    }

    public function testGmdatetime()
    {
        $now = gmdate('Y-m-d H:i:s');
        $tomorrow = gmdate('Y-m-d H:i:s', strtotime('+1day'));
        $yesterday = gmdate('Y-m-d H:i:s', strtotime('-1day'));
        $this->assertEquals($now, CDate::gmdatetime());
        $this->assertEquals($tomorrow, CDate::gmdatetime(strtotime('+1day')));
        $this->assertEquals($yesterday, CDate::gmdatetime(strtotime('-1day')));
        $this->assertEquals($yesterday, CDate::gmdatetime(CDate::datetime(strtotime('-1day'))));
    }

    public function testtimeAgo()
    {
        $this->assertEquals('1 day', CDate::time_ago(strtotime('-1day')));
        $this->assertEquals('5 days', CDate::time_ago(strtotime('-5day')));
        $this->assertEquals('1 week', CDate::time_ago(strtotime('-8day')));
        $this->assertFalse(CDate::time_ago(strtotime('-4weeks')));
        $this->assertFalse(@CDate::time_ago());
        $this->assertFalse(@CDate::time_ago(time() + MINUTE));
    }

    public function testdateRange()
    {
        $array = array(
            '2010-05-28' => 'Fri',
            '2010-05-29' => 'Sat',
            '2010-05-30' => 'Sun',
            '2010-05-31' => 'Mon',
            '2010-06-01' => 'Tue',
            '2010-06-02' => 'Wed',
            '2010-06-03' => 'Thu',
            '2010-06-04' => 'Fri',
        );
        $this->assertEquals($array, CDate::range('2010-05-28', '2010-06-04'));
        $this->assertEquals(array_flip($array), CDate::range('2010-05-28', '2010-06-04', 'D', 'Y-m-d'));
        $this->assertTrue(is_array(CDate::range(strtotime('-1week'))));
    }
}
?>
