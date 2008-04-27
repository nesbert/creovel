<?php
/*
	Class: cipher
	
	An interface class to mcrypt encryption/decryption which supports a wide variety of block algorithms.
	
	See how to create your own wrapper functions below:
	 
	(start code)
	function encrypt($str)
	{
		return cipher::encrypt($str, 2, CONST_KEY);
	}
	
	function decrypt($str)
	{
		return cipher::decrypt($str, 2, CONST_KEY);
	}
	(end)
*/

class cipher
{
	// Section: Public
	
	/*
		Function: encrypt
		
		Encrypts plaintext with given parameters. * Make sure to use the same key for
		encrypt & decrypt and also same encryption level.
		
		Parameters:
		
			str - String.
			level - Integer encryption level 1-4. 4 being the strongest/slower.
			key - Sting key used to lock/unlock encryption.
		
		Returns:
		
			String.
	*/
	
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
	
	/*
		Function: decrypt
		
		Decrypts crypttext with given parameters. * Make sure to use the same key for
		encrypt & decrypt and also same encryption level.
		
		Parameters:
		
			str - String.
			level - Integer encryption level 1-4. 4 being the strongest/slower.
			key - Sting key used to lock/unlock encryption.
		
		Returns:
		
			String.
	*/
	
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
	
	// Section: Private
	
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

}
?>