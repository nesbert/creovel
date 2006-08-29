<?php
/*
 * Log all contact requests into a table.
 */
class contact_request extends model
{
	/*
	 * States array for drop downs
	 */
	public $states = array(
							'blank-1' => 'Select a State',
							'blank-2' => '----------------',
							'AL' => 'Alabama',
							'AK' => 'Alaska',
							'AZ' => 'Arizona',
							'AR' => 'Arkansas',
							'CA' => 'California',
							'CO' => 'Colorado',
							'CT' => 'Connecticut',
							'DE' => 'Delaware',
							'DC' => 'District of Columbia',
							'FL' => 'Florida',
							'GA' => 'Georgia',
							'HI' => 'Hawaii',
							'ID' => 'Idaho',
							'IL' => 'Illinois',
							'IN' => 'Indiana',
							'IA' => 'Iowa',
							'KS' => 'Kansas',
							'KY' => 'Kentucky',
							'LA' => 'Louisiana',
							'ME' => 'Maine',
							'MD' => 'Maryland',
							'MA' => 'Massachusetts',
							'MI' => 'Michigan',
							'MN' => 'Minnesota',
							'MS' => 'Mississippi',
							'MO' => 'Missouri',
							'MT' => 'Montana',
							'NE' => 'Nebraska',
							'NV' => 'Nevada',
							'NH' => 'New Hampshire',
							'NJ' => 'New Jersey',
							'NM' => 'New Mexico',
							'NY' => 'New York',
							'NC' => 'North Carolina',
							'ND' => 'North Dakota',
							'OH' => 'Ohio',
							'OK' => 'Oklahoma',
							'OR' => 'Oregon',
							'PA' => 'Pennsylvania',
							'RI' => 'Rhode Island',
							'SC' => 'South Carolina',
							'SD' => 'South Dakota',
							'TN' => 'Tennessee',
							'TX' => 'Texas',
							'UT' => 'Utah',
							'VT' => 'Vermont',
							'VA' => 'Virginia',
							'WA' => 'Washington',
							'WV' => 'West Virginia',
							'WI' => 'Wisconsin',
							'WY' => 'Wyoming',
							'blank-3' => '----------------',
							'AS' => 'American Samoa',
							'GU' => 'Guam',
							'PR' => 'Puerto Rico',
							'VI' => 'Virgin Islands',
						);

	public function validate()
	{
		$this->validates_presence_of('name');
		$this->validates_email_of('email', $this->email, ( $this->email ? $this->email . " is not a valid email." : 'Email is a required field.' ), true);
		$this->validates_presence_of('subject');
		$this->validates_presence_of('body', $this->body, 'Comment is a required field.');
	}
	
	public function before_save()
	{
		if ( strstr($this->state, 'blank-') ) $this->state = '';		
	}

}
?>