<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers;

use PBergman\KeePass\StreamWrapper;

interface HeaderInterface
{
    /**
     * @param   StreamWrapper   $buffer
     * @return  $this
     */
    public function read(StreamWrapper $buffer);
}