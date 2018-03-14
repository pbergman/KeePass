<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Key;

use PBergman\KeePass\Exception\InvalidArgumentException;
use PBergman\KeePass\Header\HeaderInterface;

/**
 * Class StaticKey
 *
 * this is a static password implementation, meaning that password
 * and password are provided when initializing this class
 *
 * @package PBergman\KeePass\Key
 */
class StaticKey implements KeyInterface
{
    /** @var array|string  */
    protected $context = [null, null];
    /** @var string */
    protected $masterKey;

    /**
     * StaticKey constructor.
     *
     * @param null|string $pass
     * @param null|string $file
     *
     * @throws InvalidArgumentException
     */
    public function __construct($pass, $file = null)
    {
        if (empty($pass) && empty($file)) {
            throw new InvalidArgumentException("one or both of pass or file argument should be provided");
        }

        $this->context[0] = !empty($pass) ? hash('sha256', $pass, true) : null;
        $this->context[1] = !empty($file) ? $this->parseFile($file) : null;
    }


    /**
     * @{inheritdoc}
     */
    public function __debugInfo()
    {
        if (!empty($this->masterKey)) {
            return [
                'masterkey' => bin2hex($this->masterKey)
            ];
        } else {
            return [
                'pass' => bin2hex($this->context[0]),
                'file' => bin2hex($this->context[1]),
            ];
        }
    }

    /**
     * @param string $file
     * @return bool|string
     */
    protected function parseFile($file)
    {
        if (is_file($file)) {
            $file = file_get_contents($file);
        }
        if (64 === strlen($file)) {
            if (preg_match_all('/\G([a-f0-9A-F]{2})/', $file, $m)) {
                $file = implode(
                    '',
                    array_map(
                        function($value){
                            return chr(hexdec($value));
                        },
                        $m[1]
                    )
                );
            }
        } elseif (32 !== strlen($file)) {
            $file = hash('sha256', $file, true);
        }
        return $file;
    }

    /**
     * @inheritdoc
     */
    public function generate(HeaderInterface $header)
    {
        if (empty($this->context)) {
            return;
        }

        $pass = array_shift($this->context);
        $file = array_shift($this->context);

        switch ($header->getVersion()) {
            case 2:
                $key = hash('sha256', implode('', array_filter(array($pass, $file))), true);
                break;
            default:
                if ($file && $pass) {
                    $key = hash('sha256', $pass . $file , true);
                } else {
                    $key = !empty($pass) ? $pass : $file;
                }

        }

        for ($i = 0, $r = $header->getRounds(); $i < $r; $i++) {
            $key = openssl_encrypt($key, 'aes-256-ecb', $header->getSeedKey(), OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        }

        $key = hash('sha256', $key , true);
        $key = hash('sha256', $header->getSeedRandom() . $key , true);

        $this->masterKey = $key;
    }

    /**
     * @inheritdoc
     */
    public function getMasterKey()
    {
        return $this->masterKey;
    }
}