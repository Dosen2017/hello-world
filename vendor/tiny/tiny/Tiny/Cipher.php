<?php
/**
 * Created by lyx.
 * User: 446127203@qq.com
 * Date: 14-8-1
 * Time: 下午4:25
 *
 * from micromvc
 */
namespace Tiny;

class Cipher
{

    /**
     * Encrypt a string
     *
     * @param string $text to encrypt
     * @param string $key a cryptographically random string
     * @param int|string $algo the encryption algorithm
     * @param int|string $mode the block cipher mode
     * @return string
     */
    public static function encrypt($text, $key, $algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        // Create IV for encryption
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($algo, $mode), MCRYPT_RAND);

        // Encrypt text and append IV so it can be decrypted later
        $text = mcrypt_encrypt($algo, hash('sha256', $key, TRUE), $text, $mode, $iv) . $iv;

        // Prefix text with HMAC so that IV cannot be changed
        return hash('sha256', $key . $text) . $text;
    }


    /**
     * Decrypt an encrypted string
     *
     * @param string $text to encrypt
     * @param string $key a cryptographically random string
     * @param int|string $algo the encryption algorithm
     * @param int|string $mode the block cipher mode
     * @return string
     */
    public static function decrypt($text, $key, $algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        $hash = substr($text, 0, 64);
        $text = substr($text, 64);

        // Invalid HMAC?
        if(hash('sha256', $key . $text) != $hash) return;

        // Get IV off end of encrypted string
        $iv = substr($text, -mcrypt_get_iv_size($algo, $mode));

        // Decrypt string using IV and remove trailing \x0 padding added by mcrypt
        return rtrim(mcrypt_decrypt($algo, hash('sha256', $key, TRUE), substr($text, 0, -strlen($iv)), $mode, $iv), "\x0");
    }

}