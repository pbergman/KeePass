<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

namespace KeePass\KPScript\Commands;

use \Symfony\Component\Process\Process;

/**
 * Class    BasCommand
 * @package KeePass\KPScript\Commands
 */
class BasCommand
{
    /** @var  string */
    protected $command;
    /** @var string */
    static $mono;
    /** @var string */
    static $kpsLocation;
    /** @var string */
    static $dbLocation;
    /** @var string */
    static $dbPassword;

    /** @return string */
    public function buildCommand()
    {
        return sprintf(
            '%s %s "%s" %s -c:%s',
            self::$mono,
            self::$kpsLocation,
            self::$dbLocation,
            self::$dbPassword,
            $this->command
        );
    }

    /**
     * @param string $dbLocation
     */
    public static function setDbLocation($dbLocation)
    {
        self::$dbLocation = $dbLocation;
    }

    /**
     * @param string $dbPassword
     */
    public static function setDbPassword($dbPassword = null)
    {
        self::$dbPassword = !is_null($dbPassword) ? sprintf('-pw:\'%s\'',$dbPassword) : '-guikeyprompt';
    }

    /**
     * @param string $kpsLocation
     */
    public static function setKpsLocation($kpsLocation)
    {
        self::$kpsLocation = $kpsLocation;
    }

    /**
     * @param string $mono
     */
    public static function setMono($mono)
    {
        self::$mono = $mono;
    }

    /**
     * execute helper, will also catch if kps returns a error string
     *
     * @param  bool                                            $debug on true will print kps command
     * @return bool|string
     * @throws \KeePass\Exceptions\KeePassScriptErrorException
     */
    public function run($debug = false)
    {
        $command = $this->buildCommand();
        $return  = false;

        if ($debug === true) {
            echo "Running: \n$command\n";
        }

        $process = new Process($command);
        $process->run();

        if ( $process->getExitCode() === 0 ||  !preg_match('/E:\s.*/',$process->getOutput()) ) {
            $return = $process->getOutput();
        } else {
            throw new \KeePass\Exceptions\KeePassScriptErrorException($process);
        }

        return $return;
    }

}
