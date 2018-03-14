<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Crypt\Salsa20;

/**
 * Class Salsa20Cipher
 *
 * public api for the salsa20 cypher, this implementation is based on:
 *
 * http://cr.yp.to/snuffle/salsa20/regs/salsa20.c
 *
 * and as specified in:
 *
 * https://cr.yp.to/snuffle/spec.pdf
 *
 * @package PBergman\KeePass\Crypt\Salsa20
 */
interface Salsa20Interface
{
    /**
     * encryption or decryption the given string
     *
     * @param string $x
     * @return string
     */
    public function __invoke($x);


    /**
     * reset the internals
     *
     * @return void
     */
    public function reset();

}