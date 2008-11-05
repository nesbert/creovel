<?php
/**
 * An interface class to mcrypt encryption/decryption which supports a wide
 * variety of block algorithms.
 *
 * @package Creovel
 * @subpackage Creovel.Services
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 **/
class Cipher
{
	/**
	 * Encrypts plaintext with given parameters. * Make sure to use the same
	 * key for encrypt & decrypt and also same encryption level.
	 *
	 * <code>
	 * function encrypt($str) {
	 *     return Cipher::encrypt($str, 2, KEY_STRING);
	 * }
	 * </code>
	 *
	 * @param string $str
	 * @param integer $level Encryption level, 4 being the strongest/slower.
	 * @param string $key String key used to lock/unlock encryption.
	 * @return string
	 **/
	public static function encrypt($str, $level = 1, $key = 'make sure to change')
	{
		switch ($level) {
			case 4:
				$return = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, self::iv4());
			break;
			
			case 3:
				$return = mcrypt_encrypt(MCRYPT_SAFERPLUS, $key, $str, MCRYPT_MODE_ECB, self::iv3());
			break;
			
			case 2:
				$return = mcrypt_encrypt(MCRYPT_SERPENT, $key, $str, MCRYPT_MODE_ECB, self::iv2());
			break;
			
			case 1:
			default:
				$return = mcrypt_encrypt(MCRYPT_XTEA, $key, $str, MCRYPT_MODE_ECB, self::iv1());
			break;
		}
		return base64_encode($return);
	}
	
	/**
	 * Decrypts crypttext with given parameters. * Make sure to use the same
	 * key for encrypt & decrypt and also same encryption level.
	 *
	 * <code>
	 * function decrypt($str) {
	 *     return cipher::decrypt($str, 2, KEY_STRING);
	 * }
	 * </code>
	 *
	 * @param string $str
	 * @param integer $level Encryption level, 4 being the strongest/slower.
	 * @param string $key String key used to lock/unlock encryption.
	 * @return string
	 **/
	public static function decrypt($str, $level = 1, $key = 'make sure to change')
	{
		$str = base64_decode($str);
		switch ($level) {
			case 4:
				$return = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, self::iv4());
			break;
			
			case 3:
				$return = mcrypt_decrypt(MCRYPT_SAFERPLUS, $key, $str, MCRYPT_MODE_ECB, self::iv3());
			break;
			
			case 2:
				$return = mcrypt_decrypt(MCRYPT_SERPENT, $key, $str, MCRYPT_MODE_ECB, self::iv2());
			break;
			
			case 1:
			default:
				$return = mcrypt_decrypt(MCRYPT_XTEA, $key, $str, MCRYPT_MODE_ECB, self::iv1());
			break;
		}
		return trim($return);
	}
	
	private static function iv4()
	{
		return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
	}
	
	private static function iv3()
	{
		return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_SAFERPLUS, MCRYPT_MODE_ECB), MCRYPT_RAND);
	}
	
	private static function iv2()
	{
		return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_SERPENT, MCRYPT_MODE_ECB), MCRYPT_RAND);
	}
	
	private static function iv1()
	{
		return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_XTEA, MCRYPT_MODE_ECB), MCRYPT_RAND);
	}
} // END class Cipher