<?php
/*

Class: mailer

Todo:
	* auto-load attachments 
	* smtp support
	* receiving emails

*/

class mailer
{
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

	private $_attachments;
	private $_content;
	private $_content_type = 'text/plain';
	private $_content_transfer_encoding = '7bit';
	private $_mailer_name;
	private $_message_boundary;
	private $_mime_boundary;
	private $_header;
	

	// Section: Public	
	
	/*

	Function: __construct		
		Construct set message boundaries on load
	 
	*/

	public function __construct()
	{
		//echo 'mailer start<br />';
		// set message boundaries
		$this->_mailer_name = get_class($this);
		$this->_message_boundary = uniqid(rand(), true);
		$this->_mime_boundary = uniqid(rand(), true);
	}
	
	/*
	
	Function: __call	
		Magic functions.

	Parameters:	
		method - required
		args - required

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

	/*
	
	Function: set_content_type
		Sets email content type ['text/plain', 'text/html']

	Parameters:	
		type - optional

	Returns:
		type

	*/	

	public function set_content_type($type = 'text/plain')
	{
		return $this->_content_type = $type;
	}
	
	/*
	
	Function: set_content_transfer_encoding
		Sets email content transfer encoding

	Parameters:	
		encoding - optional

	Returns:
		encoding

	*/	

	public function set_content_transfer_encoding($encoding = '7bit')
	{
		return $this->_content_transfer_encoding = $encoding;
	}
	
	/*

	Function: encoded		
		Encodes the current email message into a string

	Returns:	
		string

	*/	
	public function encoded()
	{
		$return = "To: ".$this->_get_email_address($this->recipients)."\n";
		$return .= "Subject: ".$this->_get_subject()."\n";
		return $return.$this->_get_headers().$this->_get_content();
	}
	
	/*
	
	Function: send	
		Sends the current email message. Returns true on success.

	Returns:	
		bool

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
	
	/*

	Function: deliver
		Alias to send(). Returns true on success.

	Returns:
		bool	

	*/	

	public function deliver()
	{	
		return $this->send();	
	}

	/*
	
	Function: add_attachment
		Adds a attachment to the email.

	Parameters:
		file_path - path of the attachment
		content_type - type of content
		content_transfer_encoding - encoding of content

	Returns:
		string

	*/

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

	// Section: Private	
	
	/*
	
	Function: _cal
		Sets $_controller & $_action and call child method

	Parameters:	
		args - required
	 
	*/

	private function _call_action($args)
	{
		if ( method_exists($this, $this->_action) ) {
			call_user_func_array(array($this, $this->_action), $args);								
		} else {
			$_ENV['error']->add("Undefined action '{$this->_action}' in <strong>{$this->_mailer_name}</strong>");
		}
	}
	
	/*
	
	Function: _is_plain_text
		Checks if content type is 'text/plain'

	Returns:	
		bool

	*/

	private function _is_plain_text()
	{
		return ( ($this->_content_type == 'text/plain') && !$this->html ? true : false );
	}
	
	/*
	
	Function:
		Creates header of the message and loads it into $_header

	Returns:	
		string

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
	
	/*
	
	Function:
		Creates body of the message and loads it into $_content
	
	Returns:	
		string

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
	
	Function: _get_include_contents
	Insert the view into the email.

	Parameters:
		filename - path of file

	Returns:
		string or false

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
	
	/*

	Function: _get_view
		Gets the view content for message

	Return:
		string

	*/

	private function _get_view()
	{
		return $this->_get_include_contents(VIEWS_PATH.$this->_mailer_name.DS.$this->_action.'.php');
	}
	
	/*
	
	Function: _get_text
		Get text verison of message and remove all html tags from a string. it also replaces links with a text friendly link.

	Returns:	
		string

	*/

	private function _get_text()
	{
		$return = $this->text ? $this->text : $this->_get_view();
		$return = preg_replace('/<a(.*?)href="(.*?)"(.*?)>(.*?)<\\/a>/i', '$4 ($2)', $return);
		return strip_tags( $return );
	}
	
	/*
	
	Function: _get_html
		Get html verison of message

	Returns:	
		string

	*/

	private function _get_html()
	{
		return $this->_get_view();
	}
	
	/*
	
	Function: _get_email_address
		Formats email address properties into a string

	Parameters:
		email_address - email address(es)

	Return:	
		string

	*/

	private function _get_email_address($email_address)
	{	
		return ( is_array($email_address) ? implode(',', $email_address) : $email_address );
	}
	
	/*
	
	Function: _get_subject
		Returns email subject

	Returns:
		string

	*/

	private function _get_subject()
	{
		return str_replace("\n", '', $this->subject);
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
	
}
?>