<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/**
 * Emailer class.
 *
 * @todo
 *	- auto-load attachments
 *	- smtp support
 *	- receiving emails
 */
class mailer {

	/**
	 * Private class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 */
	private $_attachments;
	private $_content;
	private $_content_type = 'text/plain';
	private $_content_transfer_encoding = '7bit';
	private $_mailer_name;
	private $_message_boundary;
	private $_mime_boundary;
	private $_header;
	
	/**
	 * Public class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 */
	public $delivery_method = 'sendmail';
	public $bcc;
	public $cc;
	public $charset = 'utf-8';
	public $from;
	public $reply_to;
	public $headers;
	public $recipients;
	public $sent_on;
	public $subject;
	public $body;
	public $text;
	public $html;
	
	/**
	 * Construct set message boundaries on load
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	public function __construct()
	{
		//echo 'mailer start<br />';
		// set message boundaries
		$this->_mailer_name = get_class($this);
		$this->_message_boundary = uniqid(rand(), true);
		$this->_mime_boundary = uniqid(rand(), true);
	}
	
	/**
	 * Magic functions.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $method required
	 * @param array $args required
	 */
	public function __call($method, $args)
	{
		switch ( true ) {

			case preg_match('/^create_(.+)$/', $method, $regs):
			case preg_match('/^deliver_(.+)$/', $method, $regs):
			
				$this->_action = $regs[1];
			
				// set/call controller & action and pass arguments to child mailer class
				$this->_call_action($args);
				
				// if deliver_XXX send message
				if ( preg_match('/^deliver_(.+)$/', $method) ) {
					return $this->send();
				}
				
			break;
			
			default:
				$_ENV['error']->add("Undefined action '{$method}' in <strong>{$this->_mailer_name}</strong>");
			break;
			
		}
	}
	
	/**
	 * Sets $_controller & $_action and call child method
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param array $args required
	 */
	private function _call_action($args)
	{
		if ( method_exists($this, $this->_action) ) {
			call_user_func_array(array($this, $this->_action), $args);								
		} else {
			$_ENV['error']->add("Undefined action '{$this->_action}' in <strong>{$this->_mailer_name}</strong>");
		}
	}
	
	/**
	 * Checks if content type is 'text/plain'
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return bool
	 */
	private function _is_plain_text()
	{
		return ( ($this->_content_type == 'text/plain') && !$this->html ? true : false );
	}
	
	/**
	 * Creates header of the message and loads it into $_header
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return string
	 */
	private function _get_headers()
	{
		$this->_header = "";
		$this->_header .= $this->from ? "From: ".$this->_get_email_address($this->from)."\n" : "";
		$this->_header .= $this->reply_to ? "Reply-To: ".$this->_get_email_address($this->reply_to)."\n" : "";
		$this->_header .= $this->cc ? "Cc: ".$this->_get_email_address($this->cc)."\n" : "";
		$this->_header .= $this->bcc ? "Bcc: ".$this->_get_email_address($this->bcc)."\n" : "";
		$this->_header .= "Date: ".( $this->sent_on ? $this->sent_on : date("r") )."\n";
		
		if ( $this->_has_attachments() ) {
			$this->_header .= 'MIME-Version: 1.0'."\n";
			$this->_header .= 'Content-Type: multipart/mixed; boundary="'.$this->_mime_boundary.'"'."\n";
			$this->_header .= "--{$this->mime_boundary}"."\n";
		}
		
		if ( !$this->_is_plain_text() )  {
			$this->_header .= 'Content-Type: multipart/alternative; boundary="'.$this->_message_boundary.'"'."\n";
		}
		
		return $this->_header;
	}
	
	/**
	 * Creates body of the message and loads it into $_content
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return string
	 */
	private function _get_content()
	{
		// intialize content string
		$this->_content = '';
				
		if ( $this->_is_plain_text() ) {
		
			$this->_content = $this->_get_text();
			
		} else {
		
			// add text verison to message
			$this->_content .= "--{$this->_message_boundary}\n";
			$this->_content .= "Content-Type: text/plain; charset={$this->charset}\n";
			$this->_content .= "Content-Transfer-Encoding: {$this->content_transfer_encoding}\n";
			$this->_content .= "Content-Disposition: inline\n\n";
			$this->_content .= $this->_get_text();
			$this->_content .= "\n\n";
		
			// add html verison to message
			$this->_content .= "--{$this->_message_boundary}\n";
			$this->_content .= "Content-Type: text/html; charset={$this->charset}\n";
			$this->_content .= "Content-Transfer-Encoding: {$this->content_transfer_encoding}\n";
			$this->_content .= "Content-Disposition: inline\n\n";
			$this->_content .= $this->_get_html();
		}
		
		// get attachments string
		if ( $this->_has_attachments() ) $this->_content .= $this->_get_attachments_str();
		
		return $this->_content;	
	}
	
	/*
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 */
	private function _get_include_contents($filename)
	{
	   if ( is_file($filename) ) {
		   ob_start();
		   include $filename;
		   $contents = ob_get_contents();
		   ob_end_clean();
		   return $contents;
	   }
	   return false;
	}
	
	/**
	 * Gets the view content for message
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $str required
	 * @return string
	 */
	private function _get_view()
	{
		return $this->_get_include_contents(VIEWS_PATH.$this->_mailer_name.DS.$this->_action.'.php');
	}
	
	/**
	 * Get text verison of message and remove all html tags from a string
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $str required
	 * @return string
	 */
	private function _get_text()
	{
		return strip_tags( $this->text ? $this->text : $this->_get_view() );
	}
	
	/**
	 * Get html verison of message
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $str required
	 * @return string
	 */
	private function _get_html()
	{
		return $this->_get_view();
	}
	
	/**
	 * Formats email address properties into a string
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @access mixed $email_address required
	 * @return string
	 */
	private function _get_email_address($email_address)
	{	
		return ( is_array($email_address) ? implode(',', $email_address) : $email_address );
	}
	
	/**
	 * Returns email subject
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return string
	 */
	private function _get_subject()
	{
		return str_replace("\n", '', $this->subject);
	}
	
	/**
	 * Sets email content type ['text/plain', 'text/html']
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $type optional
	 * @return string $type
	 */	
	public function set_content_type($type = 'text/plain')
	{
		return $this->_content_type = $type;
	}
	
	/**
	 * Sets email content transfer encoding
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $encoding optional
	 * @return string $encoding
	 */	
	public function set_content_transfer_encoding($encoding = '7bit')
	{
		return $this->_content_transfer_encoding = $encoding;
	}
	
	/**
	 * Encodes the current email message into a string
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */	
	public function encoded()
	{
		$return = "To: ".$this->_get_email_address($this->recipients)."\n";
		$return .= "Subject: ".$this->_get_subject()."\n";
		return $return.$this->_get_headers().$this->_get_content();
	}
	
	/**
	 * Sends the current email message. Returns true on success.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */	
	public function send()
	{
		switch ( true ) {
		
			case ( $this->delivery_method == 'smtp' ):
				return false;
			break;
		
			case ( $this->delivery_method == 'sendmail' ):
				if ( mail($this->_get_email_address($this->recipients), $this->_get_subject(), $this->_get_content(), $this->_get_headers()) ) {
					return true;
				} else {		
					return false;
				}
			break;
			
			case ( $this->delivery_method == 'test' ):
				return true;
			break;
			
			default:
				return false;
			break;
			
		}
	}
	
	/**
	 * Alias to send(). Returns true on success.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */	
	public function deliver()
	{	
		return $this->send();	
	}
	
	/* need to finish attachment support */
	
	private function _has_attachments()
	{
		return ( count($this->_attachments) ? true : false );
	}
	
	private function _get_content_type($file_name)
	{
		return get_mime_type($file_name);	
	}
	
	private function _get_transfer_encoding($file_name)
	{
		return 'base64';
	}
	
	private function _encode_attachment($file_path)
	{
	
		if ( file_exists($file_path) ) {
	
			$file = fopen($file_path, 'r');
			$attachment = fread($file, filesize($file_path));
			$attachment = chunk_split(base64_encode($attachment));
			fclose($file);
			
			return $attachment;
		
		} else {
		
			return true;
			
		}
	
	}
	
	private function _get_attachments_str()
	{
		if ( $this->_has_attachments() ) {
	
			$return = "\n--{$this->message_boundary}--\n";
			
			foreach ( $this->_attachments as $content_id => $attachment ) {
			
				$return .= "\n--{$this->mime_boundary}\n";
				$return .= "Content-Type: {$attachment[content_type]}; name={$attachment[file_name]}\n";
				$return .= "Content-Transfer-Encoding: {$attachment[content_transfer_encoding]}\n"; 
				$return .= "Content-ID: {$content_id}\n";
				$return .= "Content-Disposition: attachment; filename=\"{$attachment[file_name]}\"\n\n";
				$return .= $attachment['content_data'];
				$return .= "\n\n";
			
			}
			
			return $return;
			
		} else {
		
			return false;
			
		}
		
	}
	
	public function add_attachment($file_path, $content_type = null, $content_transfer_encoding = null)
	{
		$key = 'attachment'.count($this->_attachments);
		$file_name = basename($file_path);
		
		$this->_attachments[$key]['content_id'] = $key;		
		$this->_attachments[$key]['content_type'] = ( $content_type ? $content_type : $this->_get_content_type($file_name) );
		$this->_attachments[$key]['content_transfer_encoding'] = ( $content_transfer_encoding ? $content_transfer_encoding : $this->_get_transfer_encoding($file_name) );
		$this->_attachments[$key]['file_name'] = $file_name;
		$this->_attachments[$key]['content_data'] = $this->_encode_attachment($file_path);
				
		return $key;
	}
}
?>