<?php
/**
 * Unit tests for ActionErrorHandler object.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'env.php';

class ActionViewTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var ActionView
     */
    protected $o;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->o = new ActionView;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->o);
    }

    public function testProcess()
    {
        $f = CREOVEL_PATH . 'views' . DS . 'layouts' . DS . '_states_dropdown_js.php';
        $r = $this->o->process($f, array('state_id' => 'TEST', 'state_input' => true));
        $s = '<script language="javascript" type="text/javascript">' . "
<!--
var US = {'AL': 'Alabama', 'AK': 'Alaska', 'AZ': 'Arizona', 'AR': 'Arkansas', 'CA': 'California', 'CO': 'Colorado', 'CT': 'Connecticut', 'DE': 'Delaware', 'FL': 'Florida', 'GA': 'Georgia', 'HI': 'Hawaii', 'ID': 'Idaho', 'IL': 'Illinois', 'IN': 'Indiana', 'IA': 'Iowa', 'KS': 'Kansas', 'KY': 'Kentucky', 'LA': 'Louisiana', 'ME': 'Maine', 'MD': 'Maryland', 'MA': 'Massachusetts', 'MI': 'Michigan', 'MN': 'Minnesota', 'MS': 'Mississippi', 'MO': 'Missouri', 'MT': 'Montana', 'NE': 'Nebraska', 'NV': 'Nevada', 'NH': 'New Hampshire', 'NJ': 'New Jersey', 'NM': 'New Mexico', 'NY': 'New York', 'NC': 'North Carolina', 'ND': 'North Dakota', 'OH': 'Ohio', 'OK': 'Oklahoma', 'OR': 'Oregon', 'PA': 'Pennsylvania', 'RI': 'Rhode Island', 'SC': 'South Carolina', 'SD': 'South Dakota', 'TN': 'Tennessee', 'TX': 'Texas', 'UT': 'Utah', 'VT': 'Vermont', 'VA': 'Virginia', 'WA': 'Washington', 'WV': 'West Virginia', 'WI': 'Wisconsin', 'WY': 'Wyoming'}
var CA = {'AB': 'Alberta', 'BC': 'British Columbia', 'MB': 'Manitoba', 'NB': 'New Brunswick', 'NL': 'Newfoundland and Labrador', 'NT': 'Northwest Territories', 'NS': 'Nova Scotia', 'NU': 'Nunavut', 'ON': 'Ontario', 'PE': 'Prince Edward Island', 'QC': 'Quebec', 'SK': 'Saskatchewan', 'YT': 'Yukon Territory'}
function updateState(country, state_id, default_value) {
    var state = document.getElementById(state_id);
    var o = '';
    
    if (country == 'US' || country == 'CA') {
        o = eval(country);
    }
    
    if (state.tagName == 'SELECT') {
        state.options.length = 0;
    }
    
    var name = state.getAttribute('name');
    var css = state.getAttribute('class');
    var title = state.getAttribute('title');
    var span = document.getElementById('TEST-wrap');
    // remove current element
    span.removeChild(state);
        
    if (o) {
        var input = document.createElement('select');
        input.setAttribute('name', name);
        input.setAttribute('id', state_id);
        if (css) input.setAttribute('class', css);
        if (title) input.setAttribute('title', title);
        span.appendChild(input);
        state = document.getElementById(state_id);
        state.options[state.options.length] = new Option('Please select...', '');
        for (var k in o) {
            state.options[state.options.length] = new Option(o[k], k);
        }
    } else {
        var input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('name', name);
        input.setAttribute('id', state_id);
        if (css) input.setAttribute('class', css);
        if (title) input.setAttribute('title', title);
        span.appendChild(input);
            }
}
-->
</script>\n";
        $this->assertEquals($s, $r);
    }

    public function testTo_str()
    {
        $l = VIEWS_PATH . DS . 'layouts' . DS . 'default.html';
        $v = VIEWS_PATH . DS . 'index' . DS . 'index.html';
        
        $this->o->_controller = 'index';
        $this->o->_action = 'index';
        $this->o->layout = 'default';
        
        $r = $this->o->to_str($v, $l);
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('layout' => false));
        $this->assertFalse(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('render' => false));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertFalse(CString::contains('<h1>Hello World!</h1>', $r));
        
        $r = $this->o->to_str($v, $l, array('render' => false, 'layout' => false));
        $this->assertFalse(CString::contains('<head>', $r));
        $this->assertFalse(CString::contains('<h1>Hello World!</h1>', $r));
        
        // render_text test
        $r = $this->o->to_str($v, $l, array('text' => 'TESTING'));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        $this->assertTrue(CString::contains('TESTING<h1>Hello World!</h1>', $r));
        
        // view <!--HEADSPLIT--> test
        $r = $this->o->to_str($v, $l, array('text' => '// comment in head<!--HEADSPLIT-->'));
        $this->assertTrue(CString::contains('<head>', $r));
        $this->assertTrue(CString::contains('<h1>Hello World!</h1>', $r));
        $this->assertTrue(CString::contains('// comment in head</head>', $r));
    }

    public function testShow()
    {
        // same as to_str but prints string out to screen.
        $this->assertTrue(method_exists($this->o, 'show'));
    }
}
?>
