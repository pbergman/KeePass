<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

use PBergman\KeePass\Exception\InvalidArgumentException;

class ResourceStream implements StreamInterface
{
    use StreamTrait;

    /**
     * ResourceStream constructor.
     *
     * @param resource $resource
     * @throws InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (is_resource($resource)) {
           $this->setResource($resource);
        }  else {
            throw new InvalidArgumentException(sprintf('expected a resource type, got %s', gettype($resource)));
        }
    }
}