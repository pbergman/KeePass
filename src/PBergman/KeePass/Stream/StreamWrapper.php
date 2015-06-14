<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Stream;

class StreamWrapper implements \Countable, \ArrayAccess
{
    /** @var resource  */
    protected $handler;

    /**
     * @inheritdoc
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw StreamException::argumentNotResource($resource);
        }

        $this->handler = $resource;

    }

    /**
     * @inheritdoc
     */
    function __destruct()
    {
        $this->close();
    }

    /**
     * closes the resource if still active
     */
    public function close()
    {
        if (is_resource($this->handler)) {
            fclose($this->handler);
        }
    }

    /**
     * register callback filter for stream
     *
     * @param   callable $callback
     * @param   int $mode
     * @return resource
     */
    public function addCallbackFilter(callable $callback, $mode = STREAM_FILTER_ALL)
    {
        $name = 'callable.' . spl_object_hash($callback);

        $this->registerFilter(
            $name,
            sprintf('%s\Filters\CallbackFilter', __NAMESPACE__)
        );

        return $this->appendFilter(
            $name ,
            $mode,
            $callback
        );
    }

    /**
     * @param   string  $name
     * @param   string  $class
     * @throws  StreamException
     */
    public function registerFilter($name, $class)
    {
        if (false === stream_filter_register($name , $class)) {
            throw new StreamException(sprintf('Could not register stream filter "%s"', $name));
        }
    }

    /**
     * append stream filter to resource, will return
     * resource to remove filter
     *
     * @param   string    $name
     * @param   int       $mode
     * @param   mixed     $params
     * @return  resource
     */
    public function appendFilter($name, $mode = STREAM_FILTER_ALL, $params = null)
    {
        return stream_filter_append($this->handler, $name, $mode, $params);
    }

    /**
     * prepend stream filter to resource, will return
     * resource to remove filter
     *
     * @param   string    $name
     * @param   int       $mode
     * @param   mixed     $params
     * @return  resource
     */
    public function prependFilter($name, $mode = STREAM_FILTER_ALL, $params = null)
    {
        return stream_filter_append($this->handler, $name, $mode, $params);
    }

    /**
     * wrapper around the stream_filter_remove function
     *
     * @param   resource    $filter
     * @return  bool
     */
    public function removeFilter($filter)
    {
        return stream_filter_remove($filter);
    }

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
     * get all (remaining) content of stream
     *
     * @param   int $maxLength
     * @param   int $offset
     * @return  string
     */
    public function getContent($maxLength = -1, $offset = -1)
    {
        return stream_get_contents($this->handler, $maxLength, $offset);
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
        return (is_null($length)) ?
            fwrite($this->handler, $data) :
            fwrite($this->handler, $data, $length);
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
     */
    public function getInfo()
    {
        return fstat($this->handler);
    }

    /**
     * Gets the bytes left on stream
     *
     * @return int
     */
    public function bytesLeft()
    {
        return $this->getInfo()['size'] - $this->getPos();
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
        return stream_get_meta_data($this->handler);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->getInfo()['size'];
    }

    /**
     * @inheritdoc
     */
    function __toString()
    {
        return (string) $this->getContent(-1, 0);
    }


    /**
     * check if given offset is left on stream
     *
     * @param   int $offset
     * @return  bool|void
     * @throws  StreamException
     */
    public function offsetExists($offset)
    {
        if (!is_numeric($offset)) {
            throw StreamException::argumentNotNumeric();
        }

        return (($this->bytesLeft() - $offset) >= 0);
    }

    /**
     * array access method to get bytes given as offset
     * from current pointer position, If offset is longer
     * than EOF the return will be rest of string.
     *
     * @param   int $offset
     * @return  string|null
     * @throws  StreamException
     */
    public function offsetGet($offset)
    {
        if (!is_numeric($offset)) {
            throw StreamException::argumentNotNumeric();
        }

        return $this->read($offset);
    }

    /**
     * array access method for stream to write given
     * data to given pointer as offset.
     *
     * for example:
     *
     * $stream => 'foo_oo';
     *
     * $stream[3] = 'bar'
     *
     * $stream => 'foobar';
     *
     * @param   int       $offset
     * @param   string    $value
     * @throws  StreamException
     */
    public function offsetSet($offset, $value)
    {
        if (!is_string($value)) {
            throw StreamException::argumentNotString();
        }

        if (!is_numeric($offset)) {
            throw StreamException::argumentNotNumeric();
        }

        $this->seek($offset);
        $this->write($value, strlen($value));
    }

    /**
     * array access for stream to truncate stream
     * to current byte. So for example:
     *
     * unset($stream[2])
     *
     * will truncate stream to 0-2 and removes all
     * after 2 pointer and stream length will be 2
     *
     * @param   int $offset
     * @throws  StreamException
     */
    public function offsetUnset($offset)
    {
        if (!is_numeric($offset)) {
            throw StreamException::argumentNotNumeric();
        }

        $this->truncate($offset);
    }

    /**
     * @return resource
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param  resource $handler
     * @throws StreamException
     * @return $this;
     */
    public function setHandler($handler)
    {
        if (!is_resource($handler)) {
            throw StreamException::argumentNotResource($handler);
        }

        $this->handler = $handler;
        return $this;
    }

    /**
     * @param  resource $handler
     * @throws StreamException
     * @return $this;
     */
    public function switchHandler($handler, $copyAll = false)
    {
        $pos = 0;
        if (!is_resource($handler)) {
            throw StreamException::argumentNotResource($handler);
        }
        if ($copyAll) {
            $pos = $this->getPos();
            $this->rewind();
        }
        while (!$this->eof()) {
            fwrite($handler, $this->read(8192));
        }
        if ($copyAll) {
            fseek($handler, $pos);
        }
        $this->close();
        $this->handler = $handler;
        return $this;
    }
}