<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Stream;

interface StreamInterface
{
    /**
     * read next X bytes from underlying stream
     *
     * @param int $size
     * @return array|string
     */
    public function read($size);

    /**
     * read all bytes left from stream
     *
     * @return string
     */
    public function readAll();

    /**
     * same as read but first moves pointer
     *
     * @param int $size
     * @param null|int $offset
     * @return array|string
     */
    public function pread($size, $offset = null);

    /**
     * set pointer to given offset
     *
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * check for end-of-file on resource pointer
     *
     * @return bool
     */
    public function eof();

    /**
     * get meta info of stream
     *
     * @return array
     */
    public function getMeta();

    /**
     * wite given data to underlying stream
     *
     * @param string $data
     * @param null|int $length
     * @return bool|int
     */
    public function write($data, $length = null);

    /**
     * return the position of the file pointer referenced
     *
     * @return int
     */
    public function tell();

    /**
     * @return array
     */
    public function stat();

    /**
     * @return bool
     */
    public function rewind();

    /**
     * @return bool
     */
    public function close();

    /**
     * Flushes the output to a file
     *
     * @return bool
     */
    public function flush();

    /**
     * Truncates a file to a given length
     *
     * @param   int $length
     * @return  bool
     */
    public function truncate($length);

    /**
     * Gets the bytes left on stream
     *
     * @return int
     */
    public function left();
//    /**
//     * Truncates and reset pointer to 0
//     */
//    public function reset();
//
//    /**
//     * will truncate buffer and write data
//     *
//     * @param $data
//     * @param null $length
//     */
//    public function rewrite($data, $length = null);
}