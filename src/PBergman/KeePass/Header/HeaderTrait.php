<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Header;

trait HeaderTrait
{
    /** @var int */
    protected $rounds;
    /** @var string  */
    protected $seedRandom;
    /** @var string  */
    protected $seedKey;
    /** @var string  */
    protected $encryptionIv;
    /** @var string  */
    protected $startBytes;

    /**
     * @return int
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @return string
     */
    public function getSeedKey()
    {
        return $this->seedKey;
    }

    /**
     * @return string
     */
    public function getSeedRandom()
    {
        return $this->seedRandom;
    }

    /**
     * @return string
     */
    public function getStartBytes()
    {
        return $this->startBytes;
    }

    /**
     * @return string
     */
    public function getEncryptionIv()
    {
        return $this->encryptionIv;
    }
}