<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Model;

class BinaryString
{
    /** @var string */
    private $data;

    /**
     * BinaryString constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function __debugInfo()
    {
        return [
            bin2hex($this->data),
        ];
    }

    /**
     * @param string $data
     * @return static
     */
    public static function fromBase64($data)
    {
        return new static(base64_decode($data, true));
    }

    /**
     * @return string
     */
    public function toBase64()
    {
        return base64_encode($this->data);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return bin2hex($this->data);
    }
}