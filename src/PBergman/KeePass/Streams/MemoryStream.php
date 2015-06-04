<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Streams;

/**
 * Class MemoryStream
 *
 * @package PBergman\KeePass\Streams
 */
class MemoryStream extends AbstractStreamWrapper
{
    /**
     * @return resource
     */
    protected function getResource($data)
    {
        if (false === $handler = fopen('php://memory', 'br+')) {
            throw new \RuntimeException('Could not create resource');
        }
        fwrite($handler, $data);
        rewind($handler);
        return $handler;
    }
}