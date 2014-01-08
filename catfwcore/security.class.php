<?php

 /**
 * Creator:      Carles Mateo
 * Date Created: 2014-01-05 12:48
 * Last Updater: Carles Mateo
 * Last Updated: 2014-01-06 15:39
 * Filename:     security.class.php
 * Description:  Methods for Security, UUID, generating ID's, maths...
 */



namespace CataloniaFramework;


abstract class Security {

    public static $s_control_string     = 'Control_String-';
    public static $s_passphrase         = 'Passphrase';
    public static $s_prefix_ciphered    = 'CFW1-';
    public static $s_algorithm          = MCRYPT_RIJNDAEL_256;
    public static $s_mode               = MCRYPT_MODE_CBC;

    public static function cipherParam($m_value, $b_base64 = true, $b_use_prefix = true) {

        // We serialize in order to be able crypt objects without corruption
        // Note: decrypt puts additional spaces at the end.
        $s_value_to_cipher = self::$s_control_string.serialize($m_value);
        $s_ciphered = mcrypt_encrypt(self::$s_algorithm, self::$s_passphrase, $s_value_to_cipher, self::$s_mode);

        if ($s_ciphered === null || $s_ciphered === '') {
            return null;
        }

        if ($b_base64 == true) {
            $s_ciphered = base64_encode($s_ciphered);
        }

        if ($b_use_prefix == true) {
            $s_ciphered = self::$s_prefix_ciphered.$s_ciphered;
        }

        return $s_ciphered;

    }

    public static function decipherParam($s_value, $b_base64 = true, $b_use_prefix = true) {

        if ($b_use_prefix == true) {
            if (substr($s_value, 0, strlen(self::$s_prefix_ciphered)) === self::$s_prefix_ciphered) {
                $s_deciphered = substr($s_value, strlen(self::$s_prefix_ciphered));
            } else {
                // The prefix is not here, error
                return null;
            }
        }

        if ($b_base64 == true) {
            $s_deciphered = base64_decode($s_deciphered);
            if ($s_deciphered === '' || $s_deciphered === null) {
                return null;
            }
        }

        $s_deciphered = mcrypt_decrypt(self::$s_algorithm, self::$s_passphrase, $s_deciphered, self::$s_mode);

        if ($s_deciphered === '' || $s_deciphered === null) {
            return null;
        }


        $i_length_control_string = strlen(self::$s_control_string);
        if (substr($s_deciphered, 0, $i_length_control_string) !== self::$s_control_string) {
            // Values do not match, we suffered injection or something went really wrong
            return null;
        }

        // Remove control string
        $s_deciphered_final = substr($s_deciphered, $i_length_control_string);

        // Remove possible ending spaces that mdecrypt might have put at the end
        $s_deciphered_final = rtrim($s_deciphered_final);

        // Unserialize to the original object
        $m_deciphered_final = unserialize($s_deciphered_final);

        return $m_deciphered_final;

    }

/**
 * UUID Methods based on code of Andrew Moore, published on php.net
 *
 * The following class generates VALID RFC 4211 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * @author Andrew Moore
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 */

	/**
	 * Generate v3 UUID
	 *
	 * Version 3 UUIDs are named based. They require a namespace (another
	 * valid UUID) and a value (the name). Given the same namespace and
	 * name, the output is always the same.
	 *
	 * @param	uuid	$namespace
	 * @param	string	$name
	 */
	public static function getUUIDV3($namespace, $name)
	{
		if(!self::isValidV3V5($namespace)) return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-','{','}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for($i = 0; $i < strlen($nhex); $i+=2)
		{
			$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
		}

		// Calculate hash value
		$hash = md5($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',

		// 32 bits for "time_low"
		substr($hash, 0, 8),

		// 16 bits for "time_mid"
		substr($hash, 8, 4),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 3
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

		// 48 bits for "node"
		substr($hash, 20, 12)
		);
	}

	/**
	 *
	 * Generate v4 UUID
	 *
	 * Version 4 UUIDs are pseudo-random.
	 */
	public static function getUUIDV4()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * Generate v5 UUID
	 *
	 * Version 5 UUIDs are named based. They require a namespace (another
	 * valid UUID) and a value (the name). Given the same namespace and
	 * name, the output is always the same.
	 *
	 * @param	uuid	$namespace
	 * @param	string	$name
	 */
	public static function getUUIDV5($namespace, $name)
	{
		if(!self::isValidV3V5($namespace)) return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-','{','}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for($i = 0; $i < strlen($nhex); $i+=2)
		{
			$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
		}

		// Calculate hash value
		$hash = sha1($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',

		// 32 bits for "time_low"
		substr($hash, 0, 8),

		// 16 bits for "time_mid"
		substr($hash, 8, 4),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 5
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

		// 48 bits for "node"
		substr($hash, 20, 12)
		);
	}

	public static function isValidV3V5($uuid) {
		return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
	}

}