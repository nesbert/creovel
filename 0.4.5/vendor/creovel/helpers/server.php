<?php
/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CNetwork object. Relying on this feature is highly discouraged.
 * 
 * General server/networking functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
 **/

/**
 * Returns browser's IP address.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function ip()
{
    return CNetwork::ip();
}

/**
 * Return the http: or https: depending on environment.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http()
{
    return CNetwork::http();
}

/**
 * Returns the current server host.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function host()
{
    return CNetwork::host();
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function http_host()
{
    return CNetwork::http_host();
}

/**
 * Returns the current server host's URL.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function url()
{
    return CNetwork::url();
}

/**
 * Returns the current server domain.
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function domain()
{
    return CNetwork::domain();
}

/**
 * A top-level domain (TLD), sometimes referred to as a top-level domain name
 * (TLDN), is the last part of an Internet domain name; that is, the letters
 * that follow the final dot of any domain name. For example, in the domain
 * name www.example.com, the top-level domain is "com".
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function tld()
{
    return CNetwork::tld();
}

/**
 * Converts a string IP to and integer and vice versa. If no $ip is passed
 * will convert $_SERVER['REMOTE_ADDR'] to an integer.
 *
 * @return mixed $ip
 * @return integer
 * @author Nesbert Hidalgo
 **/
function int_ip($ip = null)
{
    return CNetwork::int_ip($ip);
}