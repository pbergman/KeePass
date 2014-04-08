<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\KPScript\Commands;

/**
 * Class    Export
 *
 * This class exports the complete database in given format and given output
 *
 * The format is specified by the method setFormat.
 * The file to export to is specified by the method setOutput.
 *
 * @package PBergman\KeePass\KPScript\Commands
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
    /** @var string  */
    protected $command = "Export";
    /** @var int|string  */
    protected $format  = self::FORMAT_KEEPASS_2_XML;
    /** @var  string */
    protected $output;

    /**
     * builds the command for export that will be called by run
     *
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
     * will set output, can be direct like /dev/stdout or file
     *
     * @param   mixed $output
     *
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * will set format default self::FORMAT_KEEPASS_2_XML
     *
     * @param   $format
     *
     * @return  $this
     *
     * @throws  \PBergman\KeePass\Exceptions\OptionNotAllowedException
     */
    public function setFormat($format)
    {
        if (is_numeric($format)) {

            if (isset($this->allowedFormats[$format])) {
                $format = $this->allowedFormats[$format];
            } else {
                throw new \PBergman\KeePass\Exceptions\OptionNotAllowedException($format, array_keys($this->allowedFormats));
            }

        } else {

            if (!in_array($format, $this->allowedFormats)) {
                throw new \PBergman\KeePass\Exceptions\OptionNotAllowedException($format, $this->allowedFormats);
            }

        }

        $this->format = $format;

        return $this;
    }

}
