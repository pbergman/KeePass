<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\Fork\Tests;

use PBergman\KeePass\Crypt\Salsa20\Salsa20Cipher;

class SalsaTest extends \PHPUnit_Framework_TestCase
{
    public function randomHex($length = 8)
    {
        switch($length) {
            case 8:
                return mt_rand(0xa00000, 0xffffff);
                break;
            case 16:
                return implode('', array_map(function() { return mt_rand(0xa00000, 0xffffff); }, array_fill(0, 2, null)));
                break;
            case 32:
                return implode('', array_map(function() { return mt_rand(0xa00000, 0xffffff); }, array_fill(0, 4, null)));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported length: "%s", use 8, 16 or 32', $length));
        }
    }

    public function randomData($amount = 10)
    {
        return array_map(function() {
            return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, mt_rand(10, 20));
        }, array_fill(0, $amount, null));
    }

    public function testSalsa16()
    {
        foreach ([8, 12, 20] as $round) {
            foreach ([16, 32] as $keySize) {
                $key = $this->randomHex($keySize);
                $iv = $this->randomHex(8);
                $data = $this->randomData();
                $encryptedData = array();
                $salsa = new Salsa20Cipher($key, $iv, $round, Salsa20Cipher::CORE_16);
                foreach ($data as $string) {
                    $encrypted = $salsa->encrypt($string);
                    $this->assertNotEquals($string, $encrypted);
                    $encryptedData[] = $encrypted;
                }
                $salsa->reset();
                foreach ($data as $id => $string) {
                    $this->assertEquals($string, $salsa->decrypt($encryptedData[$id]));
                }
            }
        }
    }

    public function testSalsa32()
    {
        foreach ([8, 12, 20] as $round) {
            foreach ([16, 32] as $keySize) {
                $key = $this->randomHex($keySize);
                $iv = $this->randomHex(8);
                $data = $data = $this->randomData();
                $encryptedData = array();
                $salsa = new Salsa20Cipher($key, $iv, $round, Salsa20Cipher::CORE_32);
                foreach ($data as $string) {
                    $encrypted = $salsa->encrypt($string);
                    $this->assertNotEquals($string, $encrypted);
                    $encryptedData[] = $encrypted;
                }
                $salsa->reset();
                foreach ($data as $id => $string) {
                    $this->assertEquals($string, $salsa->decrypt($encryptedData[$id]));
                }
            }
        }
    }
}