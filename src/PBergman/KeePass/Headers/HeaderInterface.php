<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers;

use PBergman\KeePass\Streams\AbstractStreamWrapper;

interface HeaderInterface
{
    /**
     * @param   AbstractStreamWrapper   $buffer
     * @return  $this
     */
    public function read(AbstractStreamWrapper $buffer);
}