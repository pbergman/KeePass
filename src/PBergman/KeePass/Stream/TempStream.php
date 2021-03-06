<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

class TempStream extends ResourceStream
{
    /**
     * TempStream constructor.
     */
    public function __construct()
    {
        parent::__construct(fopen('php://temp', 'r+'));
    }

}