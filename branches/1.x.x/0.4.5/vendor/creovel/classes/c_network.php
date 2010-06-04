<?php
/**
 * Base Network class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class CNetwork extends CObject
{
    /**
     * Returns browser's IP address.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function ip()
    {
        return @$_SERVER['REMOTE_ADDR'];
    }

    /**
     * Return the http: or https: depending on environment.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function http()
    {
        return 'http'.(self::is_ssl() ? 's' : '').'://';
    }

    /**
     * Returns the current server host.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function host()
    {
        return @$_SERVER['HTTP_HOST'];
    }

    /**
     * Returns the current server host's URL.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function http_host()
    {
        return self::http() . self::host();
    }

    /**
     * Returns the current server host's URL.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function url()
    {
        return self::http_host() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the current server domain.
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function domain()
    {
        $url = explode('.', self::host());
        $tld = explode(':', $url[count($url) - 1]);
        return $url[count($url) - 2] . '.' . $tld[0];
    }

    /**
     * A top-level domain (TLD), sometimes referred to as a top-level
     * domain name (TLDN), is the last part of an Internet domain
     * name; that is, the letters that follow the final dot of any domain
     * name. For example, in the domain name www.example.com, the
     * top-level domain is "com".
     *
     * @return string
     * @author Nesbert Hidalgo
     **/
    function tld()
    {
        $url = explode('.', self::domain());
        return isset($url[1]) ? $url[1] : '';
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
        if (is_numeric($ip)) return long2ip($ip);
        return sprintf("%u", ip2long($ip ? $ip : self::ip()));
    }
    
    /**
     * Checks if application is using SSL via ENV variable HTTPS.
     *
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public function is_ssl()
    {
        return getenv('HTTPS') == 'on';
    }
    
    /**
     * Checks if application is using valid IP and also converts a
     * non-complete IP into a proper dotted quad.
     *
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public function is_ip($ip)
    {
        return long2ip(ip2long($ip)) != '0.0.0.0';
    }
} // END class CNetwork extends CObject