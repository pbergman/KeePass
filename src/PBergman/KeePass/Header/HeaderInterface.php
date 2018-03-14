<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Header;

interface HeaderInterface
{
    /**
     * @return int
     */
    public function getVersion();

    /**
     * @return int
     */
    public function getRounds();

    /**
     * @return string
     */
    public function getSeedKey();

    /**
     * @return string
     */
    public function getSeedRandom();

    /**
     * @return string
     */
    public function getStartBytes();

    /**
     * @return string
     */
    public function getEncryptionIv();

    /**
     * @return bool
     */
    public function hasCompression();

    /**
     * @return string
     */
    public function getProtectedStreamKey();
}