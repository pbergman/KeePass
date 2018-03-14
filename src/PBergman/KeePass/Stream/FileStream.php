<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

class FileStream extends ResourceStream
{
    /**
     * FileStream constructor.
     *
     * @param resource $file
     * @param string $mode
     */
    public function __construct($file, $mode = 'r')
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException("could not open or find file: ${file}");
        }

        parent::__construct(fopen($file, $mode));
    }
}