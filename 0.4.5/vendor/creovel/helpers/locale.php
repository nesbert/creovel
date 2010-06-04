<?php
/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CLocale object. Relying on this feature is highly discouraged.
 * 
 * Language and location functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
**/

/**
 * Returns an array of countries and states. Only US and Canada
 * states/provinces for now.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function countries_array($more_states = false)
{
    return CLocale::countries_array($more_states);
}

/**
 * Returns an array of countries.
 *
 * @param boolean $us_first
 * @param boolean $show_abbr
 * @return array
 * @author Nesbert Hidalgo
 **/
function countries($us_first = false, $show_abbr = false)
{
    return CLocale::countries($us_first, $show_abbr);
}

/**
 * Returns an array of states/provinces.
 *
 * @param boolean $country Default is 'US'
 * @param boolean $show_abbr
 * @param boolean $more_states
 * @return array
 * @author Nesbert Hidalgo
 **/
function states($country = 'US', $show_abbr = false, $more_states = false)
{
    return CLocale::states($country, $show_abbr, $more_states);
}

/**
 * Returns an array of timezone with GMT labels for keys and
 * timezone name as value.
 *
 * @return void
 * @author Nesbert Hidalgo
 **/
function timezones()
{
    return CLocale::timezones();
}