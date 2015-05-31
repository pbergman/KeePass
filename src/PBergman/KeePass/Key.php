<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass;

use PBergman\KeePass\Headers\AbstractHeader;

/**
 * Class Key
 *
 * @package PBergman\KeePass
 */
class Key
{
    /** @var array|string  */
    protected $password;
    /** @var null|string  */
    protected $master_key;

    /**
     * @param string|array  $password
     * @param int           $version
     * @param int           $rounds
     * @param string        $seedKey
     * @param string        $seedRandom
     */
    function __construct($password, $version, $rounds, $seedKey, $seedRandom)
    {
        $this->password   = $password;
        $this->master_key = $this->generateMasterKey($version, $rounds, $seedKey, $seedRandom);

    }

    /**
     * static helper to initialize class by header interface
     *
     * @param string|array      $password
     * @param AbstractHeader    $header
     * @return Key
     */
    static function generate($password, AbstractHeader $header)
    {
        return new self(
            $password,
            $header[$header::VERSION],
            $header[$header::ROUNDS],
            $header[$header::SEED_KEY],
            $header[$header::SEED_RAND]
        );
    }

    /**
     * @return string
     */
    public function getMasterKey()
    {
        return $this->master_key;
    }

    /**
     * @return string|array
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * convert the given password to master key that is
     * used to decrypt/encrypt the database.
     *
     * @param int               $version
     * @param int               $rounds
     * @param string            $seedKey
     * @param string            $seedRandom
     *
     * @return null|string
     */
    protected function generateMasterKey($version, $rounds, $seedKey, $seedRandom)
    {
        $file = null;

        if (is_array($this->password)) {
            list($pass, $file) = $this->password;
        } else {
            $pass = $this->password;
        }

        if (!empty($pass)) {
            $pass = hash('sha256', $pass, true);
        }

        if (!empty($file)) {
            if (is_file($file)) {
                $file = file_get_contents($file);
            }
            if (strlen($file) === 64 ) {
                if (preg_match_all('/\G([a-f0-9A-F]{2})/', $file, $m)) {
                    $file = implode('', array_map(function($value){
                        return  chr(hexdec($value));
                    }, $m[1]));
                }
            } elseif (strlen($file) !== 32 ) {
                $file =  hash('sha256', $file, true);
            }
        }

        if (!$file && !$pass) {
            throw new \RuntimeException('One or both of password or key file must be passed.');
        } elseif ($version === 2) {
            $key = hash('sha256', implode('', array_filter(array($pass, $file))), true);
        } else {
            if ($file && $pass) {
                $key = hash('sha256', $pass . $file , true);
            } else {
                $key = !empty($pass) ? $pass : $file;
            }
        }

        for ($i = 0 ; $i < $rounds; $i++) {
            $key = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $seedKey, $key, MCRYPT_MODE_ECB);
        }

        $key = hash('sha256', $key , true);
        $key = hash('sha256', $seedRandom . $key , true);

        return $key;
    }

    /**
     * @inheritdoc
     */
    function __toString()
    {
        return (string) $this->getMasterKey();
    }


}