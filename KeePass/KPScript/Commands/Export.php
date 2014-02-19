<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\KPScript\Commands;

/**
 * Class    Export
 * @package KeePass\KPScript\Commands
 */
class Export extends BasCommand
{
    const FORMAT_KEEPASS_CSV    = 0;
    const FORMAT_KEEPASS_KDB    = 1;
    const FORMAT_KEEPASS_2_KDBX = 2;
    const FORMAT_KEEPASS_2_XML  = 3;

    protected $allowedFormats = array(
        self::FORMAT_KEEPASS_CSV     => 'KeePass CSV (1.x)',
        self::FORMAT_KEEPASS_KDB     => 'KeePass KDB (1.x)',
        self::FORMAT_KEEPASS_2_KDBX  => 'KeePass KDBX (2.x)',
        self::FORMAT_KEEPASS_2_XML   => 'KeePass XML (2.x)',
    );

    protected $command = "Export";
    protected $format;
    protected $output;

    /**
     * @return string
     * @throws \Exception
     */
    public function buildCommand()
    {
        if ( empty($this->format) || empty($this->output) ) {
            throw new \Exception('Need to set format/output first');
        }

        return sprintf(
            '%s %s "%s" %s -c:%s -Format:"%s" -OutFile:"%s"',
            self::$mono,
            self::$kpsLocation,
            self::$dbLocation,
            self::$dbPassword,
            $this->command,
            $this->format,
            $this->output
        );
    }

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * will set format default self::FORMAT_KEEPASS_2_XML
     *
     * @param $format
     * @throws \KeePass\Exceptions\OptionNotAllowedException
     */
    public function setFormat($format)
    {
        $_format = self::FORMAT_KEEPASS_2_XML;

        if (is_numeric($format)) {

            if (isset($this->allowedFormats[$format])) {
                $_format = $this->allowedFormats[$format];
            } else {
                throw new \KeePass\Exceptions\OptionNotAllowedException($format, array_keys($this->allowedFormats));
            }
        } else {
            if (in_array($format, $this->allowedFormats)) {
                $_format = $format;
            } else {
                throw new \KeePass\Exceptions\OptionNotAllowedException($format, $this->allowedFormats);
            }
        }

        $this->format = $_format;
    }

}
