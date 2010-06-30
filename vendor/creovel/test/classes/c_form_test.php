<?php
/**
 * Unit tests for CForm object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class CFormTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var    CForm
     * @access protected
     */
    protected $f;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->f = new CForm;
        
        $this->date_select = '<select name="date[month]" id="date_month">
<option value="1" selected="selected">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select>
<select name="date[day]" id="date_day">
<option value="1" selected="selected">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select>
<select name="date[year]" id="date_year">
<option value="2007">2007</option>
<option value="2008">2008</option>
<option value="2009">2009</option>
<option value="2010" selected="selected">2010</option>
<option value="2011">2011</option>
<option value="2012">2012</option>
<option value="2013">2013</option>
</select>
';
        $this->time_select = '<select name="date[hour]" id="date_hour">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6" selected="selected">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select>
<select name="date[minute]" id="date_minute">
<option value="00">00</option>
<option value="01">01</option>
<option value="02">02</option>
<option value="03">03</option>
<option value="04">04</option>
<option value="05">05</option>
<option value="06">06</option>
<option value="07">07</option>
<option value="08">08</option>
<option value="09">09</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30" selected="selected">30</option>
<option value="31">31</option>
<option value="32">32</option>
<option value="33">33</option>
<option value="34">34</option>
<option value="35">35</option>
<option value="36">36</option>
<option value="37">37</option>
<option value="38">38</option>
<option value="39">39</option>
<option value="40">40</option>
<option value="41">41</option>
<option value="42">42</option>
<option value="43">43</option>
<option value="44">44</option>
<option value="45">45</option>
<option value="46">46</option>
<option value="47">47</option>
<option value="48">48</option>
<option value="49">49</option>
<option value="50">50</option>
<option value="51">51</option>
<option value="52">52</option>
<option value="53">53</option>
<option value="54">54</option>
<option value="55">55</option>
<option value="56">56</option>
<option value="57">57</option>
<option value="58">58</option>
<option value="59">59</option>
</select>
<select name="date[ampm]" id="date_ampm">
<option value="AM">AM</option>
<option value="PM" selected="selected">PM</option>
</select>
';
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testName_to_id()
    {
        $this->assertEquals('first_name', CForm::name_to_id('first_name'));
        $this->assertEquals('name_first', CForm::name_to_id('name[first]'));
    }

    public function testAdd_error()
    {
        CForm::add_error('first_name', 'First name is required');
        $this->assertEquals(
            'First name is required',
            $GLOBALS['CREOVEL']['VALIDATION_ERRORS']['first_name']
            );
    }

    public function testField_has_error()
    {
        CForm::add_error('first_name', 'First name is required');
        $this->assertTrue(CForm::field_has_error('first_name'));
    }

    public function testHas_error()
    {
        CForm::add_error('first_name', 'First name is required');
        $this->assertTrue(CForm::has_error());
    }

    public function testError_count()
    {
        CForm::add_error('first_name', 'First name is required');
        CForm::add_error('last_name', 'Last name is required');
        $this->assertEquals(2, CForm::error_count());
    }

    public function testError_messages_for()
    {
        CForm::add_error('first_name', 'First name is required');
        $o = '<div class="errors">
<div class="top"></div>
<div class="body">
<h1 class="error_title">1 error has prohibited this Form from being saved.</h1><ul>
<li>First name is required</li>
</ul>
</div>
<div class="bottom"></div>
</div>
';
        
        $this->expectOutputString($o);
        echo CForm::error_messages_for();
    }

    public function testStart_form()
    {
        $this->assertEquals(
            '<form method="post" id="form_a" name="form_a" action="a">'."\n",
            CForm::start_form(
                array('controller'=>'a')
                )
            );
        $this->expectOutputString('<form method="post" id="form_a" name="form_a" action="a/b">
');
        echo CForm::start_form(
                array('controller'=>'a', 'action'=>'b')
                );
    }

    public function testEnd_form()
    {
        $this->assertEquals("</form>\n", CForm::end_form());
    }

    public function testInput()
    {
        $this->assertEquals(
            '<input type="text" id="a" name="a" />'."\n",
            CForm::input('text', 'a'));
        $this->assertEquals(
            '<input type="text" id="a" name="a" value="b" />'."\n",
            CForm::input('text', 'a', 'b'));
        $this->assertEquals(
            '<input type="text" id="a" name="a" value="b" class="c" />'."\n",
            CForm::input('text', 'a', 'b', array('class' => 'c')));
        $this->assertEquals(
            '<input type="checkbox" id="a_d" name="a" value="d" class="c" />'."\n",
            CForm::input('checkbox', 'a', 'b', array('class' => 'c'), 'd'));
        $this->assertEquals(
            '<input type="checkbox" id="a_d" name="a" value="d" class="c" checked="checked" />'."\n",
            CForm::input('checkbox', 'a', 'd', array('class' => 'c'), 'd'));
        $this->assertEquals(
            '<input type="checkbox" id="a_d" name="a" value="d" class="c" checked="checked" /> APPEND'."\n",
            CForm::input('checkbox', 'a', 'd', array('class' => 'c'), 'd', 'APPEND'));
    }

    /**
     * @depends testInput
     */
    public function testText_field()
    {
        $this->assertEquals(
            '<input type="text" id="a" name="a" />'."\n",
            CForm::text_field('a'));
        $this->assertEquals(
            '<input type="text" id="a" name="a" value="b" />'."\n",
            CForm::text_field('a', 'b'));
    }

    /**
     * @depends testInput
     */
    public function testHidden_field()
    {
        $this->assertEquals(
            '<input type="hidden" id="a" name="a" />'."\n",
            CForm::hidden_field('a'));
        $this->assertEquals(
            '<input type="hidden" id="a" name="a" value="b" />'."\n",
            CForm::hidden_field('a', 'b'));
    }

    /**
     * @depends testInput
     */
    public function testPassword_field()
    {
        $this->assertEquals(
            '<input type="password" id="a" name="a" autocomplete="off" />'."\n",
            CForm::password_field('a'));
        $this->assertEquals(
            '<input type="password" id="a" name="a" value="b" autocomplete="off" />'."\n",
            CForm::password_field('a', 'b'));
    }

    /**
     * @depends testInput
     */
    public function testRadio_button()
    {
        $this->assertEquals(
            '<input type="radio" id="a" name="a" value="" checked="checked" />'."\n",
            CForm::radio_button('a'));
        $this->assertEquals(
            '<input type="radio" id="a" name="a" value="" />'."\n",
            CForm::radio_button('a', 'b'));
        $this->assertEquals(
            '<input type="radio" id="a" name="a" value="" class="c" />'."\n",
            CForm::radio_button('a', 'b', array('class' => 'c')));
        $this->assertEquals(
            '<input type="radio" id="a_b" name="a" value="b" checked="checked" />'."\n",
            CForm::radio_button('a', 'b', null, 'b'));
        $this->assertEquals(
            '<input type="radio" id="a_b" name="a" value="b" checked="checked" /> TEST'."\n",
            CForm::radio_button('a', 'b', null, 'b', 'TEST'));
    }

    /**
     * @depends testInput
     */
    public function testCheck_box()
    {
        $this->assertEquals(
            '<input type="checkbox" id="a" name="a" value="" checked="checked" />'."\n",
            CForm::check_box('a'));
        $this->assertEquals(
            '<input type="checkbox" id="a" name="a" value="" />'."\n",
            CForm::check_box('a', 'b'));
        $this->assertEquals(
            '<input type="checkbox" id="a" name="a" value="" class="c" />'."\n",
            CForm::check_box('a', 'b', array('class' => 'c')));
        $this->assertEquals(
            '<input type="checkbox" id="a_b" name="a" value="b" checked="checked" />'."\n",
            CForm::check_box('a', 'b', null, 'b'));
        $this->assertEquals(
            '<input type="checkbox" id="a_b" name="a" value="b" checked="checked" /> TEST'."\n",
            CForm::check_box('a', 'b', null, 'b', 'TEST'));
    }

    /**
     * @depends testInput
     */
    public function testSubmit()
    {
        $this->assertEquals(
            '<input type="submit" value="Submit" />'."\n",
            CForm::submit());
        $this->assertEquals(
            '<input type="submit" value="Click Here" class="b" />'."\n",
            CForm::submit('Click Here', array('class' => 'b')));
    }

    /**
     * @depends testInput
     */
    public function testButton()
    {
        $this->assertEquals(
            '<input type="button" value="Button" />'."\n",
            CForm::button());
        $this->assertEquals(
            '<input type="button" value="Click Here" class="b" />'."\n",
            CForm::button('Click Here', array('class' => 'b')));
    }

    public function testTextarea()
    {
        $this->assertEquals(
            '<textarea id="a" name="a"></textarea>'."\n",
            CForm::textarea('a'));
        $this->assertEquals(
            '<textarea id="a" name="a" class="c">b</textarea>'."\n",
            CForm::textarea('a', 'b', array('class' => 'c')));
    }

    /**
     * @depends testInput
     */
    public function testText_area()
    {
        $this->assertEquals(
            '<textarea id="a" name="a"></textarea>'."\n",
            CForm::text_area('a'));
        $this->assertEquals(
            '<textarea id="a" name="a" class="c">b</textarea>'."\n",
            CForm::text_area('a', 'b', array('class' => 'c')));
    }

    public function testLabel()
    {
        $this->assertEquals(
            '<label for="a">A</label>'."\n",
            CForm::label('a'));
        $this->assertEquals(
            '<label class="c" for="a">b</label>'."\n",
            CForm::label('a', 'b', array('class' => 'c')));
    }

    public function testSelect()
    {
        $this->assertEquals(
            '<select name="a" id="a">
<option value="x">option x</option>
<option value="y">option y</option>
<option value="z">option z</option>
</select>
',
            CForm::select('a', 'b', array(
                    'x' => 'option x',
                    'y' => 'option y',
                    'z' => 'option z'
                    )));
        $this->assertEquals(
            '<select name="a" id="a">
<option value="x">option x</option>
<option value="y">option y</option>
<option value="z" selected="selected">option z</option>
</select>
',
            CForm::select('a', 'z', array(
                    'x' => 'option x',
                    'y' => 'option y',
                    'z' => 'option z'
                    )));
    }

    /**
     * @depends testSelect
     */
    public function testSelect_states()
    {
                $this->assertEquals(
                    '<span id="state-wrap"><select name="state" id="state">
<option value="" selected="selected">Please select...</option>
<option value="AL">Alabama</option>
<option value="AK">Alaska</option>
<option value="AZ">Arizona</option>
<option value="AR">Arkansas</option>
<option value="CA">California</option>
<option value="CO">Colorado</option>
<option value="CT">Connecticut</option>
<option value="DE">Delaware</option>
<option value="FL">Florida</option>
<option value="GA">Georgia</option>
<option value="HI">Hawaii</option>
<option value="ID">Idaho</option>
<option value="IL">Illinois</option>
<option value="IN">Indiana</option>
<option value="IA">Iowa</option>
<option value="KS">Kansas</option>
<option value="KY">Kentucky</option>
<option value="LA">Louisiana</option>
<option value="ME">Maine</option>
<option value="MD">Maryland</option>
<option value="MA">Massachusetts</option>
<option value="MI">Michigan</option>
<option value="MN">Minnesota</option>
<option value="MS">Mississippi</option>
<option value="MO">Missouri</option>
<option value="MT">Montana</option>
<option value="NE">Nebraska</option>
<option value="NV">Nevada</option>
<option value="NH">New Hampshire</option>
<option value="NJ">New Jersey</option>
<option value="NM">New Mexico</option>
<option value="NY">New York</option>
<option value="NC">North Carolina</option>
<option value="ND">North Dakota</option>
<option value="OH">Ohio</option>
<option value="OK">Oklahoma</option>
<option value="OR">Oregon</option>
<option value="PA">Pennsylvania</option>
<option value="RI">Rhode Island</option>
<option value="SC">South Carolina</option>
<option value="SD">South Dakota</option>
<option value="TN">Tennessee</option>
<option value="TX">Texas</option>
<option value="UT">Utah</option>
<option value="VT">Vermont</option>
<option value="VA">Virginia</option>
<option value="WA">Washington</option>
<option value="WV">West Virginia</option>
<option value="WI">Wisconsin</option>
<option value="WY">Wyoming</option>
</select>
</span>
',
                    CForm::select_states());
    }

    /**
     * @depends testSelect
     */
    public function testSelect_countries()
    {
        $this->assertEquals(
            '<select name="country" id="country">
<option value="" selected="selected">Please select...</option>
<option value="AF">Afghanistan</option>
<option value="AL">Albania</option>
<option value="DZ">Algeria</option>
<option value="AS">American Samoa</option>
<option value="AD">Andorra</option>
<option value="AO">Angola</option>
<option value="AI">Anguilla</option>
<option value="AQ">Antarctica</option>
<option value="AG">Antigua and Barbuda</option>
<option value="AR">Argentina</option>
<option value="AM">Armenia</option>
<option value="AW">Aruba</option>
<option value="AU">Australia</option>
<option value="AT">Austria</option>
<option value="AZ">Azerbaijan</option>
<option value="BS">Bahamas</option>
<option value="BH">Bahrain</option>
<option value="BD">Bangladesh</option>
<option value="BB">Barbados</option>
<option value="BY">Belarus</option>
<option value="BE">Belgium</option>
<option value="BZ">Belize</option>
<option value="BJ">Benin</option>
<option value="BM">Bermuda</option>
<option value="BT">Bhutan</option>
<option value="BO">Bolivia</option>
<option value="BA">Bosnia and Herzegovina</option>
<option value="BW">Botswana</option>
<option value="BV">Bouvet Island</option>
<option value="BR">Brazil</option>
<option value="IO">British Indian Ocean Territory</option>
<option value="BN">Brunei Darussalam</option>
<option value="BG">Bulgaria</option>
<option value="BF">Burkina Faso</option>
<option value="BI">Burundi</option>
<option value="KH">Cambodia</option>
<option value="CM">Cameroon</option>
<option value="CA">Canada</option>
<option value="CV">Cape Verde</option>
<option value="KY">Cayman Islands</option>
<option value="CF">Central African Republic</option>
<option value="TD">Chad</option>
<option value="CL">Chile</option>
<option value="CN">China</option>
<option value="CX">Christmas Island</option>
<option value="CC">Cocos (Keeling) Islands</option>
<option value="CO">Colombia</option>
<option value="KM">Comoros</option>
<option value="CG">Congo</option>
<option value="CD">Congo, Democratic Republic of the</option>
<option value="CK">Cook Islands</option>
<option value="CR">Costa Rica</option>
<option value="CI">Cote d\'Ivoire</option>
<option value="HR">Croatia</option>
<option value="CU">Cuba</option>
<option value="CY">Cyprus</option>
<option value="CZ">Czech Republic</option>
<option value="DK">Denmark</option>
<option value="DJ">Djibouti</option>
<option value="DM">Dominica</option>
<option value="DO">Dominican Republic</option>
<option value="TP">East Timor</option>
<option value="EC">Ecuador</option>
<option value="EG">Egypt</option>
<option value="SV">El Salvador</option>
<option value="GQ">Equatorial Guinea</option>
<option value="ER">Eritrea</option>
<option value="EE">Estonia</option>
<option value="ET">Ethiopia</option>
<option value="FK">Falkland Islands (Malvinas)</option>
<option value="FO">Faroe Islands</option>
<option value="FJ">Fiji</option>
<option value="FI">Finland</option>
<option value="FR">France</option>
<option value="GF">French Guiana</option>
<option value="PF">French Polynesia</option>
<option value="TF">French Southern Territories</option>
<option value="GA">Gabon</option>
<option value="GM">Gambia</option>
<option value="GE">Georgia</option>
<option value="DE">Germany</option>
<option value="GH">Ghana</option>
<option value="GI">Gibraltar</option>
<option value="GR">Greece</option>
<option value="GL">Greenland</option>
<option value="GD">Grenada</option>
<option value="GP">Guadeloupe</option>
<option value="GU">Guam</option>
<option value="GT">Guatemala</option>
<option value="GN">Guinea</option>
<option value="GW">Guinea-Bissau</option>
<option value="GY">Guyana</option>
<option value="HT">Haiti</option>
<option value="HM">Heard Island and McDonald Islands</option>
<option value="VA">Holy See (Vatican City)</option>
<option value="HN">Honduras</option>
<option value="HK">Hong Kong</option>
<option value="HU">Hungary</option>
<option value="IS">Iceland</option>
<option value="IN">India</option>
<option value="ID">Indonesia</option>
<option value="IR">Iran, Islamic Republic of</option>
<option value="IQ">Iraq</option>
<option value="IE">Ireland</option>
<option value="IL">Israel</option>
<option value="IT">Italy</option>
<option value="JM">Jamaica</option>
<option value="JP">Japan</option>
<option value="JO">Jordan</option>
<option value="KZ">Kazakstan</option>
<option value="KE">Kenya</option>
<option value="KI">Kiribati</option>
<option value="KP">Korea, Democratic People\'s Republic of</option>
<option value="KR">Korea, Republic of</option>
<option value="KW">Kuwait</option>
<option value="KG">Kyrgyzstan</option>
<option value="LA">Lao People\'s Democratic Republic</option>
<option value="LV">Latvia</option>
<option value="LB">Lebanon</option>
<option value="LS">Lesotho</option>
<option value="LR">Liberia</option>
<option value="LY">Libyan Arab Jamahiriya</option>
<option value="LI">Liechtenstein</option>
<option value="LT">Lithuania</option>
<option value="LU">Luxembourg</option>
<option value="MO">Macau</option>
<option value="MK">Macedonia, The Former Yugoslav Republic of</option>
<option value="MG">Madagascar</option>
<option value="MW">Malawi</option>
<option value="MY">Malaysia</option>
<option value="MV">Maldives</option>
<option value="ML">Mali</option>
<option value="MT">Malta</option>
<option value="MH">Marshall Islands</option>
<option value="MQ">Martinique</option>
<option value="MR">Mauritania</option>
<option value="MU">Mauritius</option>
<option value="YT">Mayotte</option>
<option value="MX">Mexico</option>
<option value="FM">Micronesia, Federated States of</option>
<option value="MD">Moldova, Republic of</option>
<option value="MC">Monaco</option>
<option value="MN">Mongolia</option>
<option value="MS">Montserrat</option>
<option value="MA">Morocco</option>
<option value="MZ">Mozambique</option>
<option value="MM">Myanmar</option>
<option value="NA">Namibia</option>
<option value="NR">Nauru</option>
<option value="NP">Nepal</option>
<option value="NL">Netherlands</option>
<option value="AN">Netherlands Antilles</option>
<option value="NC">New Caledonia</option>
<option value="NZ">New Zealand</option>
<option value="NI">Nicaragua</option>
<option value="NE">Niger</option>
<option value="NG">Nigeria</option>
<option value="NU">Niue</option>
<option value="NF">Norfolk Island</option>
<option value="MP">Northern Mariana Islands</option>
<option value="NO">Norway</option>
<option value="OM">Oman</option>
<option value="PK">Pakistan</option>
<option value="PW">Palau</option>
<option value="PS">Palestinian Territory, Occupied</option>
<option value="PA">PANAMA</option>
<option value="PG">Papua New Guinea</option>
<option value="PY">Paraguay</option>
<option value="PE">Peru</option>
<option value="PH">Philippines</option>
<option value="PN">Pitcairn</option>
<option value="PL">Poland</option>
<option value="PT">Portugal</option>
<option value="PR">Puerto Rico</option>
<option value="QA">Qatar</option>
<option value="RE">Reunion</option>
<option value="RO">Romania</option>
<option value="RU">Russian Federation</option>
<option value="RW">Rwanda</option>
<option value="SH">Saint Helena</option>
<option value="KN">Saint Kitts and Nevis</option>
<option value="LC">Saint Lucia</option>
<option value="PM">Saint Pierre and Miquelon</option>
<option value="VC">Saint Vincent and the Grenadines</option>
<option value="WS">Samoa</option>
<option value="SM">San Marino</option>
<option value="ST">Sao Tome and Principe</option>
<option value="SA">Saudi Arabia</option>
<option value="SN">Senegal</option>
<option value="SC">Seychelles</option>
<option value="SL">Sierra Leone</option>
<option value="SG">Singapore</option>
<option value="SK">Slovakia</option>
<option value="SI">Slovenia</option>
<option value="SB">Solomon Islands</option>
<option value="SO">Somalia</option>
<option value="ZA">South Africa</option>
<option value="GS">South Georgia and the South Sandwich Islands</option>
<option value="ES">Spain</option>
<option value="LK">Sri Lanka</option>
<option value="SD">Sudan</option>
<option value="SR">Suriname</option>
<option value="SJ">Svalbard and Jan Mayen</option>
<option value="SZ">Swaziland</option>
<option value="SE">Sweden</option>
<option value="CH">Switzerland</option>
<option value="SY">Syrian Arab Republic</option>
<option value="TW">Taiwan, Province of China</option>
<option value="TJ">Tajikistan</option>
<option value="TZ">Tanzania, United Republic of</option>
<option value="TH">Thailand</option>
<option value="TG">Togo</option>
<option value="TK">Tokelau</option>
<option value="TO">Tonga</option>
<option value="TT">Trinidad and Tobago</option>
<option value="TN">Tunisia</option>
<option value="TR">Turkey</option>
<option value="TM">Turkmenistan</option>
<option value="TC">Turks and Caicos Islands</option>
<option value="TV">Tuvalu</option>
<option value="UG">Uganda</option>
<option value="UA">Ukraine</option>
<option value="AE">United Arab Emirates</option>
<option value="GB">United Kingdom</option>
<option value="US">United States</option>
<option value="UM">United States Minor Outlying Islands</option>
<option value="UY">Uruguay</option>
<option value="UZ">Uzbekistan</option>
<option value="VU">Vanuatu</option>
<option value="VE">Venezuela</option>
<option value="VN">Viet Nam</option>
<option value="VG">Virgin Islands, British</option>
<option value="VI">Virgin Islands, U.S.</option>
<option value="WF">Wallis and Futuna</option>
<option value="EH">Western Sahara</option>
<option value="YE">Yemen</option>
<option value="YU">Yugoslavia</option>
<option value="ZM">Zambia</option>
<option value="ZW">Zimbabwe</option>
</select>
',
            CForm::select_countries());
    }

    /**
     * @depends testSelect
     */
    public function testSelect_redirect()
    {
        $this->assertEquals(
            '<select name="shortcut" id="shortcut" onchange="location.href=this.options[this.selectedIndex].value;">
<option value="http://apple.com">Apple</option>
<option value="https://google.com">Google</option>
</select>
',
            CForm::select_redirect(
                'shortcut',
                array(
                    'http://apple.com' => 'Apple',
                    'https://google.com' => 'Google'
                    )
                ));
    }

    /**
     * @todo Implement testDate_select().
     */
    public function testDate_select()
    {
        $this->assertEquals(
            $this->date_select,
            CForm::date_select(
                'date',
                '2010-01-01'
                ));
    }

    /**
     * @depends testSelect
     */
    public function testTime_select()
    {
        
        $this->assertEquals($this->time_select, CForm::time_select('date', '2010-01-01 18:30:00'));
        $this->assertEquals($this->time_select, CForm::time_select('date', '18:30'));
        $this->assertEquals($this->time_select, CForm::time_select('date', '6:30 pm'));
        $this->assertEquals($this->time_select, CForm::time_select('date', '01-01-01 6:30 pm'));
        $this->assertEquals($this->time_select, CForm::time_select('date', array(
            'hour' => '18',
            'minute' => '30'
            )));
    }

    /**
     * @depends testSelect
     */
    public function testDate_time_select()
    {
        $this->assertEquals($this->date_select.' @ '.$this->time_select, CForm::date_time_select('date', '2010-01-01 18:30:00'));
    }

    public function testGet_timestamp_from_post()
    {
        $key = 'date';
        $_POST[$key]['month'] = 6;
        $_POST[$key]['day'] = 22;
        $_POST[$key]['year'] = 2001;
        $_POST[$key]['hour'] = 11;
        $_POST[$key]['minute'] = 30;
        $_POST[$key]['ampm'] = 'pm';
        $this->assertEquals(993277800, CForm::get_timestamp_from_post($key));
    }

    /**
     * @depends testSelect
     */
    public function testGet_timestamp_from_array()
    {
        $key = 'date';
        $_POST[$key]['month'] = 6;
        $_POST[$key]['day'] = 22;
        $_POST[$key]['year'] = 2001;
        $_POST[$key]['hour'] = 11;
        $_POST[$key]['minute'] = 30;
        $_POST[$key]['ampm'] = 'pm';
        $this->assertEquals(993277800, CForm::get_timestamp_from_array($_POST[$key]));
    }

    /**
     * @depends testSelect
     */
    public function testSelect_time_zone()
    {
        $t = '<select name="a" id="a">
<option value="" selected="selected">Please select...</option>
<option value="US & Canada">US/Pacific</option>
<option value="-10:00 Hawaii">US/Hawaii</option>
<option value="-09:00 Alaska">US/Alaska</option>
<option value="-08:00 Pacific Time">US/Pacific</option>
<option value="-08:00 Pacific Time (Yukon)">Canada/Yukon</option>
<option value="-07:00 Arizona">US/Arizona</option>
<option value="-07:00 Mountain Time">US/Mountain</option>
<option value="-06:00 Central Time">US/Central</option>
<option value="-06:00 Saskatchewan">Canada/Saskatchewan</option>
<option value="-06:00 Saskatchewan (East)">Canada/East-Saskatchewan</option>
<option value="-05:00 Eastern Time">US/Eastern</option>
<option value="-05:00 Eastern Time (Michigan)">US/Michigan</option>
<option value="-05:00 Indiana (East)">US/East-Indiana</option>
<option value="-05:00 Indiana (Starke)">US/Indiana-Starke</option>
<option value="-04:00 Atlantic Time (Canada)">Canada/Atlantic</option>
<option value="-03:30 Newfoundland">Canada/Newfoundland</option>
<option value="International">GMT</option>
<option value="-12:00 Eniwetok, Kwajalein">Pacific/Kwajalein</option>
<option value="-11:00 Midway Island, Samoa">US/Samoa</option>
<option value="-06:00 Central America">Etc/GMT-6</option>
<option value="-06:00 Mexico City">America/Mexico_City</option>
<option value="-05:00 Bogota, Lima, Quito">America/Bogota</option>
<option value="-04:00 Caracas, La Paz">America/Caracas</option>
<option value="-04:00 Santiago">America/Santiago</option>
<option value="-03:00 Brasilia">Brazil/West</option>
<option value="-03:00 Greenland">Etc/GMT-3</option>
<option value="-02:00 Mid-Atlantic">Etc/GMT-2</option>
<option value="-01:00 Azores">Atlantic/Azores</option>
<option value="-01:00 Cape Verde Is.">Atlantic/Cape_Verde</option>
<option value="GMT Casablanca, Monrovia">Africa/Casablanca</option>
<option value="Greenwich Mean Time GMT: Dublin, Edinburgh, Lisbon, London">GMT</option>
<option value="+01:00 Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna">Etc/GMT+1</option>
<option value="+01:00 Belgrade, Bratislava, Budapest, Ljubljana, Prague">Etc/GMT+1</option>
<option value="+01:00 Brussels, Copenhagen, Madrid, Paris">Etc/GMT+1</option>
<option value="+01:00 Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb">Etc/GMT+1</option>
<option value="+01:00 West Central Africa">Etc/GMT+1</option>
<option value="+02:00 Athens, Istanbul, Minsk">Etc/GMT+2</option>
<option value="+02:00 Bucharest">Etc/GMT+2</option>
<option value="+02:00 Cairo">Etc/GMT+2</option>
<option value="+02:00 Harare, Pretoria">Etc/GMT+2</option>
<option value="+02:00 Helsinki, Riga, Tallinn">Etc/GMT+2</option>
<option value="+02:00 Jerusalem">Etc/GMT+2</option>
<option value="+03:00 Baghdad">Etc/GMT+3</option>
<option value="+03:00 Kuwait, Riyadh">Etc/GMT+3</option>
<option value="+03:00 Moscow, St. Petersburg, Volgograd">Etc/GMT+3</option>
<option value="+03:00 Nairobi">Etc/GMT+3</option>
<option value="+03:30 Tehran">Etc/GMT+3</option>
<option value="+04:00 Abu Dhabi, Muscat">Etc/GMT+4</option>
<option value="+04:00 Baku, bilisi, erevan">Etc/GMT+4</option>
<option value="+04:30 Kabul">Asia/Kabul</option>
<option value="+05:00 Ekaterinburg">Etc/GMT+5</option>
<option value="+05:00Islamabad, Karachi, Tashkent">Etc/GMT+5</option>
<option value="+05:30 Calcutta, Chennai, Mumbai, New Delhi">Asia/Calcutta</option>
<option value="+05:45 Kathmandu">Asia/Katmandu</option>
<option value="+06:00 Almatay, Novosibirsk">Etc/GMT+6</option>
<option value="+06:00 Astana, Dhaki">Etc/GMT+6</option>
<option value="+06:00 Sri Jayawardenepura">Etc/GMT+6</option>
<option value="+06:30 Rangoon">Asia/Rangoon</option>
<option value="+07:00 Bangkok, Hanoi, Jakarta">Etc/GMT+7</option>
<option value="+07:00 Krasnoyarsk">Etc/GMT+7</option>
<option value="+08:00Beijing, Chongqing, Hong Kong, Urumqi">Etc/GMT+8</option>
<option value="+08:00 Irkutsk, Ulaan Bataar">Etc/GMT+8</option>
<option value="+08:00 Kuala Lumpur, Singapore">Etc/GMT+8</option>
<option value="+08:00 Perth">Etc/GMT+8</option>
<option value="+08:00Taipei">Etc/GMT+8</option>
<option value="+09:00 Osaka, Sapporo, Tokyo">Etc/GMT+9</option>
<option value="+09:00 Seoul">Etc/GMT+9</option>
<option value="+09:00 Yakutsk">Etc/GMT+9</option>
<option value="+09:30 Adelaide">Etc/GMT+9</option>
<option value="+09:30 Darwin">Australia/Darwin</option>
<option value="+10:00 Brisbane">Etc/GMT+10</option>
<option value="+10:00 Canberra, Melbourne, Sydney">Etc/GMT+10</option>
<option value="+10:00 Guam, Port Moresby">Etc/GMT+10</option>
<option value="+10:00 Hobart">Etc/GMT+10</option>
<option value="+10:00 Vladivostok">Etc/GMT+10</option>
<option value="+11:00 Magadan, Solomon Is., New Caledonia">Etc/GMT+11</option>
<option value="+12:00 Auckland, ellington">Etc/GMT+12</option>
<option value="+12:00 Fiji, Kamchatka, Marshall Is.">Etc/GMT+12</option>
</select>
';
        $this->assertEquals($t, CForm::select_time_zone('a'));
    }

    public function testCheckbox_select()
    {
        $ops = array(
            'w' => 'Apple',
            'x' => 'Microsoft',
            'y' => 'Yahoo!',
            'z' => 'Google',
            );
        $t  = '<divclass="checks-css">
<label class=" row row-1" for="a_w">
<input type="checkbox" id="a_w" name="a" value="w" checked="checked" /> Apple
</label>
<label class=" row row-2" for="a_x">
<input type="checkbox" id="a_x" name="a" value="x" /> Microsoft
</label>
<label class=" row row-1" for="a_y">
<input type="checkbox" id="a_y" name="a" value="y" checked="checked" /> Yahoo!
</label>
<label class=" row row-2" for="a_z">
<input type="checkbox" id="a_z" name="a" value="z" /> Google
</label>
</div>
';
        $this->assertEquals($t, CForm::checkbox_select('a', array('w','y'), $ops, array('class' => 'checks-css')));
    }

    /**
     * @todo Implement testRadio_select().
     */
    public function testRadio_select()
    {
        $ops = array(
            'w' => 'Apple',
            'x' => 'Microsoft',
            'y' => 'Yahoo!',
            'z' => 'Google',
            );
        $t  = '<divclass="checks-css">
<label class=" row row-1" for="a_w">
<input type="radio" id="a_w" name="a" value="w" checked="checked" /> Apple
</label>
<label class=" row row-2" for="a_x">
<input type="radio" id="a_x" name="a" value="x" /> Microsoft
</label>
<label class=" row row-1" for="a_y">
<input type="radio" id="a_y" name="a" value="y" /> Yahoo!
</label>
<label class=" row row-2" for="a_z">
<input type="radio" id="a_z" name="a" value="z" /> Google
</label>
</div>
';
        $this->assertEquals($t, CForm::radio_select('a', array('w'), $ops, array('class' => 'checks-css')));

    }
}
?>
