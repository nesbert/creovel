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
 * Common contants declared here.
 */

define(DS, DIRECTORY_SEPARATOR);
define(SECOND,  1);
define(MINUTE, 60 * SECOND);
define(HOUR,   60 * MINUTE);
define(DAY,    24 * HOUR);
define(WEEK,    7 * DAY);
define(MONTH,  30 * DAY);
define(YEAR,  365 * DAY);

$_ENV['countries'] = array('US' => array('name' => 'United States', 
										'states' => array(
										'AK' => 'Alaska',
										'AL' => 'Alabama',
										'AR' => 'Arkansas',
										'AS' => 'American Samoa',
										'AZ' => 'Arizona',
										'CA' => 'California',
										'CO' => 'Colorado',
										'CT' => 'Connecticut',
										'DC' => 'D.C.',
										'DE' => 'Delaware',
										'FL' => 'Florida',
										'FM' => 'Micronesia',
										'GA' => 'Georgia',
										'GU' => 'Guam',
										'HI' => 'Hawaii',
										'IA' => 'Iowa',
										'ID' => 'Idaho',
										'IL' => 'Illinois',
										'IN' => 'Indiana',
										'KS' => 'Kansas',
										'KY' => 'Kentucky',
										'LA' => 'Louisiana',
										'MA' => 'Massachusetts',
										'MD' => 'Maryland',
										'ME' => 'Maine',
										'MH' => 'Marshall Islands',
										'MI' => 'Michigan',
										'MN' => 'Minnesota',
										'MO' => 'Missouri',
										'MP' => 'Marianas',
										'MS' => 'Mississippi',
										'MT' => 'Montana',
										'NC' => 'North Carolina',
										'ND' => 'North Dakota',
										'NE' => 'Nebraska',
										'NH' => 'New Hampshire',
										'NJ' => 'New Jersey',
										'NM' => 'New Mexico',
										'NV' => 'Nevada',
										'NY' => 'New York',
										'OH' => 'Ohio',
										'OK' => 'Oklahoma',
										'OR' => 'Oregon',
										'PA' => 'Pennsylvania',
										'PR' => 'Puerto Rico',
										'PW' => 'Palau',
										'RI' => 'Rhode Island',
										'SC' => 'South Carolina',
										'SD' => 'South Dakota',
										'TN' => 'Tennessee',
										'TX' => 'Texas',
										'UT' => 'Utah',
										'VA' => 'Virginia',
										'VI' => 'Virgin Islands',
										'VT' => 'Vermont',
										'WA' => 'Washington',
										'WI' => 'Wisconsin',
										'WV' => 'West Virginia',
										'WY' => 'Wyoming',
										'AA' => 'Military Americas',
										'AE' => 'Military Europe/ME/Canada',
										'AP' => 'Military Pacific'
									)
							),
					'CA' => array(	'name' => 'Canada', 
									'states' => array(
										'AB' => 'Alberta',
 										'MB' => 'Manitoba',
										'AB' => 'Alberta',
										'BC' => 'British Columbia',
										'MB' => 'Manitoba',
										'NB' => 'New Brunswick',
										'NL' => 'Newfoundland and Labrador',
										'NS' => 'Nova Scotia',
										'NT' => 'Northwest Territories',
										'NU' => 'Nunavut',
										'ON' => 'Ontario',
										'PE' => 'Prince Edward Island',
										'QC' => 'Quebec',
										'SK' => 'Saskatchewan',
										'YT' => 'Yukon Territory',
									)
					)
	);
?>