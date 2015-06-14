<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Stream;

class TempStream extends StreamWrapper
{
    /** @var StreamWrapper  */
    protected $stream;

    public function __construct(StreamWrapper $stream)
    {
        parent::__construct(fopen('php://temp', 'r+b'));
        $this->stream = $stream;
    }


    public function save()
    {
        $this->stream->reset();
        $this->rewind();
        while (!$this->eof()) {
            $this->stream->write($this->read(8192), 8192);
        }
        $this->stream->rewind();
        $this->close();
    }

}