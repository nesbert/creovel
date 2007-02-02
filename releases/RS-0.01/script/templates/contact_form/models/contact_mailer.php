<?php
class contact_mailer extends mailer
{

	public function contact($contact)
	{
		$this->contact = (object) $contact;
		
		$this->set_content_type('text/html');
		$this->recipients = $this->contact->to_email;
		$this->from = $this->contact->email;
		$this->subject = $this->contact->subject;
	}

}
?>