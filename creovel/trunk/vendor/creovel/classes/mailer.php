<?php
/**
 * Emailer class.
 * @package Mailer
 *
 * @author Nesbert Hidalgo
 */
 
class mailer extends controller {

	public $message_boundary;
	public $mime_boundary;
	public $charset = 'utf-8';
	public $content_transfer_encoding = '7bit';
	public $header;
	public $message;
	
	public $from;
	public $to;
	public $cc;
	public $bcc;
	public $subject;
	public $html;
	public $text;	
	public $attachments;
	
	protected $view_content;
	
	private $test_mode = false;
	
	public function __construct()
	{
	
		// set message boundarries
		$this->message_boundary = uniqid(rand(), true);
		$this->mime_boundary = uniqid(rand(), true);
	
	}
	
	public function __destruct()
	{
	
		// set message boundary
		//print_obj($this->message);
	
	}
	
	public function __call($method, $args)
	{
		
		switch ( true ) {

			case preg_match('/^create_(.+)$/', $method, $regs):
				// set class variables
				$this->render = $regs[1];
				$this->action = $regs[1];
				// call method
				$this->$regs[1]($args[0]);
				// set view content
				$this->set_view_content();
				// build message
				$this->build_message();
			break;
				
			default:
				$x = debug_backtrace();
				trigger_error('Undefined method <b>$' . $method . '</b> in class <b>\'' . get_class($this) . '\'</b>  in page <b>\'' . $x[0]['file'] . '\'</b> on line <b>' . $x[0]['line'] .' </b>.', E_USER_ERROR);
			break;
			
		}
		
	}
	
	public function get_from_address()
	{	
		return $this->from;
	}	
	
	public function get_email_address($email_address)
	{	
		return ( is_array($email_address) ? implode(',', $email_address) : $email_address );
	}
	
	public function send()
	{
	
		if ( $this->test_mode === true ) return true;
		
		if ( mail($this->get_email_address($this->to), $this->subject, $this->message, $this->header) ) {
		
			return true;
		
		} else {
		
			return false;
			
		}
	
	}
	
	public function deliver()
	{
	
		return $this->send();
	
	}
	
	private function set_header() {
	
		$this->header = '';
		$this->header .= $this->cc ? "Cc: ".$this->get_email_address($this->cc)."\n" : "";
		$this->header .= $this->bcc ? "Bcc: ".$this->get_email_address($this->bcc)."\n" : "";
		$this->header .= "From: ".$this->get_from_address();
		$this->header .= "\nDate: ".date("r");
		$this->header .= "\nMIME-Version: 1.0\n";
		
		if ( $this->has_attachments() ) {
		
			$this->header .= 'Content-Type: multipart/mixed; boundary="'.$this->mime_boundary.'"';
			$this->header .= "\n";
			$this->header .= "--{$this->mime_boundary}\n";
			
		}
		
		$this->header .= 'Content-Type: multipart/alternative; boundary="'.$this->message_boundary.'"';
		
	}
	
	private function set_view_content()
	{
		$this->view_content = $this->get_include_contents(VIEWS_PATH.get_class($this).DS.$this->render.'.php');	
	}
	
	private function build_message()
	{
	
		// set message header
		$this->set_header();
		
		// create messsage string
		$this->message = "\n";
		
		if ( $this->text !== false ) {
		
			// if text not set use view_content
			if ( !$this->text ) $this->text = $this->view_content;
			
			// add text verison to message
			$this->message .= "--{$this->message_boundary}\n";
			$this->message .= "Content-Type: text/plain; charset={$this->charset}\n";
			$this->message .= "Content-Transfer-Encoding: {$this->content_transfer_encoding}\n";
			$this->message .= "Content-Disposition: inline\n\n";
			$this->message .= $this->text;
			
			$this->message .= "\n\n";
		
		}
		
		if ( $this->html !== false ) {
		
			// if html not set use view_content
			if ( !$this->html ) $this->html = $this->view_content;
			
			// insert html into layout (template) for html verison of the message
			if ( $this->layout !== false ) {
			
				$template_path = VIEWS_PATH.'layouts'.DS.$this->layout.'.php';
				$html_body = str_replace('@@content@@', $this->html, $this->get_include_contents($template_path));
				
			}
			
			// add html verison to message
			$this->message .= "--{$this->message_boundary}\n";
			$this->message .= "Content-Type: text/html; charset={$this->charset}\n";
			$this->message .= "Content-Transfer-Encoding: {$this->content_transfer_encoding}\n";
			$this->message .= "Content-Disposition: inline\n\n";
			$this->message .= ($html_body ? $html_body : $this->html);
			
		}
		
		$this->message .= "\n--{$this->message_boundary}--\n";
		
		// create messsage string
		$this->message .= $this->get_attachments();
		
	}
	
	public function add_attachment($file_path, $content_type = null, $content_transfer_encoding = null)
	{
		$key = 'attachment'.count($this->attachments);
		$file_name = basename($file_path);
		
		$this->attachments[$key]['content_id'] = $key;		
		$this->attachments[$key]['content_type'] = ( $content_type ? $content_type : $this->get_content_type($file_name) );
		$this->attachments[$key]['content_transfer_encoding'] = ( $content_transfer_encoding ? $content_transfer_encoding : $this->get_transfer_encoding($file_name) );
		$this->attachments[$key]['file_name'] = $file_name;
		$this->attachments[$key]['content_data'] = $this->encode_attachment($file_path);
				
		return $key;
	}
	
	private function get_content_type($file_name)
	{
		return get_mime_type($file_name);	
	}
	
	private function get_transfer_encoding($file_name)
	{
		return 'base64';
	}
	
	private function encode_attachment($file_path)
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
	
	private function get_attachments()
	{
		if ( $this->has_attachments() ) {
	
			$return = '';
			
			foreach ( $this->attachments as $content_id => $attachment ) {
			
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
	
	private function has_attachments()
	{
	
		return ( count($this->attachments) ? true : false );
	
	}
	
}
?>