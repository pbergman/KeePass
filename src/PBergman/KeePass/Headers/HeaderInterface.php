<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers;

interface HeaderInterface
{
    /**
     * read from header from resource
     *
     * @param  resource $fd
     * @return $this
     */
    public function read($fd);
}