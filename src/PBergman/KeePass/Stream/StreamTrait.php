<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

trait StreamTrait
{
    /** @var resource  */
    private $resource;

    /**
     * @param resource $resource
     */
    private function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        return stream_get_meta_data($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function read($size)
    {
        return fread($this->resource, $size);
    }

    /**
     * @inheritdoc
     */
    public function readAll()
    {
        return $this->read($this->stat()['size'] - $this->tell());
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function stat()
    {
        return fstat($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function write($data, $length = null)
    {
        return (is_null($length)) ? fwrite($this->resource, $data) : fwrite($this->resource, $length, $length);
    }

    /**
     * @inheritdoc
     */
    public function pread($size, $offset = null)
    {
        $this->seek($offset);
        return $this->read($size);
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->resource, $offset, $whence);
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        return ftell($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        return rewind($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return fclose($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        return fflush($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function truncate($length)
    {
        return ftruncate($this->resource, $length);
    }

    /**
     * @inheritdoc
     */
    public function left()
    {
        return $this->stat()['size'] - $this->tell();
    }

//    /**
//     * @inheritdoc
//     */
//    public function reset()
//    {
//        $this->truncate(0);
//        $this->flush();
//        $this->rewind();
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function rewrite($data, $length = null)
//    {
//        $this->reset();
//        $this->write($data, $length);
//        $this->rewind();
//    }
}