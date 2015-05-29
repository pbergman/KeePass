<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Headers\V1;

use PBergman\KeePass\Database\StreamWrapper;
use PBergman\KeePass\Headers\AbstractHeader;
use PBergman\KeePass\KeePass;

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
     * @inheritdoc
     */
    public function read($fd)
    {
        if (!is_resource($fd)) {
            throw new \InvalidArgumentException(sprintf('Expected a resource got %s', gettext($fd)));
        }

        $stats = fstat($fd);

        if ($stats['size'] <  $this[self::HEADER_SIZE]) {
            throw HeaderException::invalidFileSize($stats);
        }

        $ret = unpack('Lflags/Lver/a16seed_rand/a16enc_iv/Ln_groups/Ln_entries/a32checksum/a32seed_key/Lrounds', fread($fd, 116));

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
