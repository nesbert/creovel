<?php
class contact_controller extends application_controller
{

	public function index()
	{
		$this->run('form');	
	}
	
	public function form()
	{
		$this->contact = new contact_request($this->params['contact_request']);		
		
		if ( $_POST  ) {
		
			$this->contact->to_email = 'nhidalgo@propertyline.com';
			
			// save info to database		
			if ( $this->contact->save() ) {
			
				// create email
				$email = new contact_mailer();
				$email->create_contact($this->contact->values());
				
				// send email
				if ( $email->send() ) {
					redirect_to('contact', 'sent');
				} else {
					$this->contact->errors->add('none', 'A problem occured while trying to send your message. Please try again.');
				}
				
			}
		
		}
	}
	
	public function sent()
	{}
	
}
?>