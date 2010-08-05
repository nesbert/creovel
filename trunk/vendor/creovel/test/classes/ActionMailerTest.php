<?php
/**
 * Unit tests for ActionMailer object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActionMailerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ActionMailer
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new ActionMailer;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
    }

    public function testInitialize_action_mailer()
    {
        $this->assertTrue(method_exists($this->o, 'initialize_parents'));
        $this->o->initialize_parents();
    }

    public function test__call()
    {
        // make suer magic function call exists
        $this->assertTrue(method_exists($this->o, '__call'));
    }

    public function testSet_content_type()
    {
        $this->assertEquals('text/html', $this->o->set_content_type('html'));
        $this->assertEquals('text/plain', $this->o->set_content_type('text'));
        $this->assertEquals('text/html', $this->o->set_content_type('text/html'));
        $this->assertEquals('test', $this->o->set_content_type('test'));
        $this->assertEquals('text/plain', $this->o->set_content_type());
    }

    public function testSet_content_transfer_encoding()
    {
        $this->assertEquals('7bit', $this->o->set_content_transfer_encoding());
        $this->assertEquals('8bit', $this->o->set_content_transfer_encoding('8bit'));
    }

    public function testEncoded()
    {
        $this->o->subject = "Test\n\nEmail";
        $this->o->to = 'John Doe <to@test.com>';
        $this->o->from = 'from@test.com';
        $this->o->reply_to = 'reply_to@test.com';
        $this->o->cc = 'cc@test.com';
        $this->o->bcc = 'bcc@test.com';
        $this->o->text = '<a href="http://apple.com">Apple</a>';
        $this->o->html = 'Visit <a href="http://apple.com">Apple</a>';
        
        $s = $this->o->encoded();
        $this->assertTrue(strstr($s, 'To: John Doe <to@test.com>') !== false);
        $this->assertTrue(strstr($s, 'Subject: Test Email') !== false);
        $this->assertTrue(strstr($s, 'From: from@test.com') !== false);
        $this->assertTrue(strstr($s, 'Reply-To: reply_to@test.com') !== false);
        $this->assertTrue(strstr($s, 'Cc: cc@test.com') !== false);
        $this->assertTrue(strstr($s, 'Bcc: bcc@test.com') !== false);
        $this->assertTrue(strstr($s, 'Date: ' . date('r')) !== false);
        $this->assertTrue(strstr($s, 'Content-Type: multipart/alternative;') !== false);
        $this->assertTrue(strstr($s, 'Content-Type: text/plain; charset=utf-8') !== false);
        $this->assertTrue(strstr($s, 'Content-Transfer-Encoding: 7bit') !== false);
        $this->assertTrue(strstr($s, 'Content-Disposition: inline') !== false);
        $this->assertTrue(strstr($s, 'Content-Type: text/html; charset=utf-8') !== false);
        $this->assertTrue(strstr($s, 'Apple (http://apple.com)') !== false);
        $this->assertTrue(strstr($s, 'Visit <a href="http://apple.com">Apple</a>') !== false);
    }

    public function testSend()
    {
        // alias to send
        $this->assertTrue(method_exists($this->o, 'send'));
        $this->o->delivery_method = 'test';
        $this->assertTrue($this->o->send());
    }

    /**
     * @depends testSend
     */
    public function testDeliver()
    {
        // alias to send
        $this->assertTrue(method_exists($this->o, 'deliver'));
        $this->o->delivery_method = 'test';
        $this->assertTrue($this->o->deliver());
    }

    public function testIs_plain_text()
    {
        $this->assertTrue($this->o->is_plain_text());
        $this->o->set_content_type('html');
        $this->assertFalse($this->o->is_plain_text());
        $this->o->set_content_type('text');
        $this->assertTrue($this->o->is_plain_text());
        $this->o->html = 'test';
        $this->assertFalse($this->o->is_plain_text());
    }

    public function testGet_headers()
    {
        $this->o->from = 'from@test.com';
        $this->o->reply_to = 'reply_to@test.com';
        $this->o->cc = 'cc@test.com';
        $this->o->bcc = 'bcc@test.com';
        
        $s = $this->o->get_headers();
        
        $this->assertTrue(strstr($s, 'From: ' . $this->o->from) !== false);
        $this->assertTrue(strstr($s, 'Reply-To: ' . $this->o->reply_to) !== false);
        $this->assertTrue(strstr($s, 'Cc: ' . $this->o->cc) !== false);
        $this->assertTrue(strstr($s, 'Bcc: ' . $this->o->bcc) !== false);
        $this->assertTrue(strstr($s, 'Date: ' . date('r')) !== false);
    }

    public function testGet_content()
    {
        $this->assertEquals('', $this->o->get_content());
        $this->o->text = '<p>TEST</p>';
        $this->assertEquals('TEST', $this->o->get_content());
        $this->o->text = '<a href="http://apple.com">Apple</a>';
        $this->assertEquals('Apple (http://apple.com)', $this->o->get_content());
        $this->o->html = 'Visit <a href="http://apple.com">Apple</a>';
        
        $s = $this->o->get_content();
        $this->assertTrue(strstr($s, 'Content-Type: text/plain; charset=utf-8') !== false);
        $this->assertTrue(strstr($s, 'Content-Transfer-Encoding: 7bit') !== false);
        $this->assertTrue(strstr($s, 'Content-Disposition: inline') !== false);
        $this->assertTrue(strstr($s, 'Content-Type: text/html; charset=utf-8') !== false);
    }

    public function testGet_text()
    {
        $this->assertEquals('', $this->o->get_text());
        $this->o->text = '<p>TEST</p>';
        $this->assertEquals('TEST', $this->o->get_text());
        $this->o->text = '<a href="http://apple.com">Apple</a>';
        $this->assertEquals('Apple (http://apple.com)', $this->o->get_text());
        $this->o->html = 'test <a href="http://apple.com">Apple</a>';
        $this->assertEquals('Apple (http://apple.com)', $this->o->get_text());
        $this->o->text = '';
        $this->assertEquals('test Apple (http://apple.com)', $this->o->get_text());
    }

    public function testGet_html()
    {
        $this->assertEquals('', $this->o->get_html());
        $this->o->html = '<p>TEST</p>';
        $this->assertEquals('<p>TEST</p>', $this->o->get_html());
        
    }

    public function testGet_email_address()
    {
        $emails = 'test@test.com';
        $this->assertEquals($emails, $this->o->get_email_address($emails));
        $emails = 'test@test.com,test1@test.com';
        $this->assertEquals($emails, $this->o->get_email_address($emails));
        $emails = 'test@test.com,test1@test.com';
        $this->assertEquals($emails, $this->o->get_email_address(explode(',', $emails)));
    }

    public function testGet_subject()
    {
        $this->o->subject = 'TEST';
        $this->assertEquals($this->o->subject, $this->o->get_subject());
        $this->o->subject = "TEST\nTEST";
        $this->assertEquals("TEST TEST", $this->o->get_subject());
        $this->o->subject = "TEST\n\nTEST";
        $this->assertEquals("TEST TEST", $this->o->get_subject());
    }

    public function testAdd_attachment()
    {
        $f = VIEWS_PATH . 'layouts' . DS . 'default.html';
        $this->assertEquals('attachment1', $this->o->add_attachment($f));
    }

    public function testHas_attachments()
    {
        $this->assertEquals(0, $this->o->has_attachments());
        $f = VIEWS_PATH . 'layouts' . DS . 'default.html';
        $this->assertEquals('attachment1', $this->o->add_attachment($f));
        $this->assertEquals(1, $this->o->has_attachments());
    }

    public function testGet_transfer_encoding()
    {
        $this->assertEquals('base64', $this->o->get_transfer_encoding());
    }

    public function testEncode_attachment()
    {
        $f = VIEWS_PATH . 'layouts' . DS . 'default.html';
        $s = $this->o->encode_attachment($f);
        $this->assertEquals(file_get_contents($f), base64_decode($s));
    }

    public function testGet_attachments_str()
    {
        $this->assertFalse($this->o->get_attachments_str());
        $f = VIEWS_PATH . 'layouts' . DS . 'default.html';
        $this->assertEquals('attachment1', $this->o->add_attachment($f));
        $s = $this->o->get_attachments_str();
        $this->assertTrue(strstr($s, 'Content-Type: text/html; name=default.html') !== false);
        $this->assertTrue(strstr($s, 'Content-Transfer-Encoding: base64') !== false);
        $this->assertTrue(strstr($s, 'Content-ID: attachment1') !== false);
        $this->assertTrue(strstr($s, 'Content-Disposition: attachment; filename="default.html"') !== false);
    }
}
?>
