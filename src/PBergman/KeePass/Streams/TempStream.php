<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Streams;

/**
 * Class TempStream
 *
 * @package PBergman\KeePass\Streams
 */
class TempStream extends AbstractStreamWrapper
{
    /**
     * @return resource
     */
    protected function getResource($data)
    {
        if (false === $handler = fopen('php://memory', 'r+b')) {
            throw new \RuntimeException('Could not create resource');
        }
        fwrite($handler, $data);
        rewind($handler);
        return $handler;
    }
}