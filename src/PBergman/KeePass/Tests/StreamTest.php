<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Fork\Tests;

use PBergman\KeePass\StreamWrapper;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function getStream()
    {
        return new StreamWrapper(fopen('php://memory', 'w+'));
    }

    public function testInstance()
    {
        $stream = $this->getStream();
        $this->assertInstanceOf('PBergman\KeePass\StreamWrapper', $stream);
        $this->assertSame('resource', gettype($stream->getHandler()));
    }

    /**
     * @depends testInstance
     */
    public function testWrite()
    {
        $stream = $this->getStream();
        $this->assertSame(100, $stream->write(str_repeat("\x0", 100)));
        $this->assertSame(10,  $stream->write(str_repeat("\x0", 100), 10));
    }

    /**
     * @depends testWrite
     */
    public function testRead()
    {
        $stream = $this->getStream();
        $stream->write(str_repeat("\x0", 100));
        $stream->rewind();
        $this->assertSame(str_repeat("\x0", 10), $stream->read(10));
        $this->assertSame(str_repeat("\x0", 90), $stream->getContent());
        $this->assertSame(str_repeat("\x0", 100), $stream->getContent(-1, 0));
        $this->assertSame(100, count($stream));
        $this->assertSame(str_repeat("\x0", 100), (string)$stream);
        $this->assertTrue($stream->eof());
        $stream->seek(80);
        $this->assertSame(20, $stream->bytesLeft());
    }

    /**
     * @depends testRead
     */
    public function testRewrite()
    {
        $stream = $this->getStream();
        $stream->write(str_repeat("A", 100));
        $this->assertSame(str_repeat("A", 100), $stream->getContent(-1, 0));
        $stream->rewrite(str_repeat("B", 100));
        $this->assertSame(str_repeat("B", 100), $stream->getContent());
    }

    /**
     * @depends testRewrite
     */
    public function testReset()
    {
        $stream = $this->getStream();
        $stream->write(str_repeat("A", 100));
        $stream->rewind();
        $this->assertSame(str_repeat("A", 100), $stream->getContent());
        $this->assertSame(100, $stream->getInfo()['size']);
        $stream->reset();
        $this->assertSame(0, $stream->getInfo()['size']);
    }

    /**
     * @depends testWrite
     */
    public function testArrayAccess()
    {
        $stream = $this->getStream();
        $stream->write('AABBCCDDEEEEFFFFFFFFFF');
        $stream->rewind();
        $this->assertSame('AA', $stream[2]);
        $this->assertSame('BB', $stream[2]);
        $this->assertSame('CC', $stream[2]);
        $this->assertSame('DD', $stream[2]);
        $this->assertSame('EEEE', $stream[4]);
        $this->assertSame('FFFFFFFFFF', $stream[10]);
        $this->assertSame(22, count($stream));
        unset($stream[10]);
        $this->assertSame(10, count($stream));
        $this->assertSame('AABBCCDDEE', $stream->getContent(-1, 0));
        $stream[10] = 'TTXXRRSSSS';
        $this->assertSame(20, count($stream));
        $this->assertSame('AABBCCDDEETTXXRRSSSS', $stream->getContent(-1, 0));
        $this->assertFalse(isset($stream[2]));
        $this->assertTrue(isset($stream[0]));
        $stream->seek(18);
        $this->assertTrue(isset($stream[2]));
    }

}