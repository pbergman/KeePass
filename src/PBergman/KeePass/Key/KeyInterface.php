<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Key;

use PBergman\KeePass\Header\HeaderInterface;

interface KeyInterface
{
    /**
     * this will be called to generate a master key
     * that can be used for decrypting or encrypting
     * the database.
     *
     * @param HeaderInterface $header
     */
    public function generate(HeaderInterface $header);

    /**
     * @return string
     */
    public function getMasterKey();
}