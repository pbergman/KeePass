<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace PBergman\KeePass\Streams;

abstract class AbstractStreamWrapper implements \Countable
{
    /** @var resource  */
    protected $handler;
    /** @var array  */
    protected $meta;

    /**
     * @inheritdoc
     */
    public function __construct($data)
    {
        $this->handler = $this->getResource($data);
        $this->meta = stream_get_meta_data($this->handler);
    }

    /**
     * @inheritdoc
     */
    function __destruct()
    {
        if (is_resource($this->handler)) {
            fclose($this->handler);
        }
    }

    /**
     * @return resource
     */
    abstract protected function getResource($data);

    /**
     * get $size bytes from stream
     *
     * @param   $size
     * @return  string
     */
    public function read($size)
    {
        return fread($this->handler, $size);
    }

    /**
     * Truncates and reset pointer to 0
     */
    public function reset()
    {
        $this->truncate(0);
        $this->flush();
        $this->rewind();
    }

    /**
     * will truncate buffer and write data
     *
     * @param $data
     * @param null $length
     */
    public function rewrite($data, $length = null)
    {
        $this->reset();
        $this->write($data, $length);
        $this->rewind();

    }

    /**
     * Truncates a file to a given length
     *
     * @param   int $length
     * @return  bool
     */
    public function truncate($length)
    {
        return ftruncate($this->handler, $length);
    }

    /**
     * Flushes the output to a file
     *
     * @return bool
     */
    public function flush()
    {
        return fflush($this->handler);
    }

    /**
     * get all content of stream
     *
     * @param   bool        $rewind         is true will rewind en get the stream content
     * @return  string
     */
    public function getContent($rewind = false)
    {
        if (false === $rewind) {
            return $this->read($this->left());
        } else {
            $this->rewind();
            return $this->read($this->getInfo('size'));
        }
    }

    /**
     *  Tests for end-of-file on a file pointer
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->handler);
    }

    /**
     * write data to stream
     *
     * @param   string    $data
     * @param   int|null  $length
     * @return  int
     */
    public function write($data, $length = null)
    {
        return (is_null($length)) ? fwrite($this->handler, $data) : fwrite($this->handler, $data, $length);
    }

    /**
     * Rewind the position of stream pointer
     *
     * @return bool
     */
    public function rewind()
    {
        return rewind($this->handler);
    }

    /**
     * Get the current position of the stream pointer
     *
     * @return int
     */
    public function getPos()
    {
        return ftell($this->handler);
    }

    /**
     * Gets information about stream
     *
     * @return  mixed
     * @throws \InvalidArgumentException
     */
    public function getInfo($name = null)
    {
        if (!$name) {
            return fstat($this->handler);
        } else {
            $info = fstat($this->handler);
            if (!isset($info[$name])) {
                throw new \InvalidArgumentException(sprintf('Noting defined in the info for name: "%s"', $name));
            } else {
                return $info[$name];
            }
        }
    }

    /**
     * Gets the bytes left on stream
     *
     * @return int
     */
    public function left()
    {
        return $this->getInfo('size') - $this->getPos();
    }

    /**
     * Seeks on a file pointer
     *
     * @param   int $offset
     * @param   int $whence
     * @return  int
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->handler, $offset, $whence);
    }

    /**
     * get meta info of stream
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->getInfo('size');
    }

    /**
     * @inheritdoc
     */
    function __toString()
    {
        return (string) $this->getContent(true);
    }
}