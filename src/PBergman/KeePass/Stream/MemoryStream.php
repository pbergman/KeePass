<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

class MemoryStream extends ResourceStream
{
    /**
     * MemoryStream constructor.
     */
    public function __construct()
    {
        parent::__construct( fopen('php://memory', 'r+'));
    }
}