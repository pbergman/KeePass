<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers\V1;

use PBergman\KeePass\Stream\StreamWrapper;
use PBergman\KeePass\Headers\AbstractHeader;

class Header extends AbstractHeader
{
    const SIG_2 = 0xB54BFB65;
    const DB_VER_DW = 0x00030002;
    const FLAG_RIJNDAEL = 2;
    const FLAG_TWOFISH  = 8;


    const C_GROUPS = 9;
    const C_ENTRIES = 10;
    const CHECKSUM = 11;
    const FLAGS = 12;

    public function __construct()
    {
        parent::__construct(13);
    }

    /**
     * set default for header
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [
            self::VERSION => 1,
            self::HEADER_SIZE => 124,
        ];
    }

    /**
     * @param   StreamWrapper $buffer
     * @throws  HeaderException
     * @return  $this
     */
    public function read(StreamWrapper $buffer)
    {

        if (count($buffer) <  $this[self::HEADER_SIZE]) {
            throw HeaderException::invalidFileSize(count($buffer));
        }

        $ret = unpack('Lflags/Lver/a16seed_rand/a16enc_iv/Ln_groups/Ln_entries/a32checksum/a32seed_key/Lrounds', $buffer->read(116));

        foreach($ret as $key => $value) {
            $this[$key] = $value;
        }

        if ($this['ver'] & 0xFFFFFF00 != self::DB_VER_DW & 0xFFFFFF00) {
            throw HeaderException::unsupportedVersion($this['ver']);
        }

        switch (true) {
            case $this[self::FLAGS] & self::FLAG_RIJNDAEL:
                $this[self::ENC_TYPE] = 'rijndael';
                break;
            case $this[self::FLAGS] & self::FLAG_TWOFISH:
                $this[self::ENC_TYPE] = 'twofish';
                break;
            default:
                throw HeaderException::UnknownEncryptionType();

        }

    }

    /**
     * @inheritdoc
     */
    protected function getConstants()
    {
        return parent::getConstants([
            'SIG_1',
            'SIG_2',
            'DB_VER_DW',
            'FLAG_RIJNDAEL',
            'FLAG_TWOFISH',
        ]);
    }

}
