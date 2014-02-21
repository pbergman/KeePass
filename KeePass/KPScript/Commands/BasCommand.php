<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
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
    /** @var string|callable */
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
     * @param bool $dbPassword
     */
    public static function setDbPassword($dbPassword = false)
    {
        self::$dbPassword = $dbPassword;
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
     * and sets password by callback or gui when nothing is set
     *
     *
     * @param  bool         $debug on true will print kps command
     * @param  bool         $raw   on true will return raw output
     * @return bool|string
     * @throws \KeePass\Exceptions\KeePassScriptErrorException
     */
    public function run($debug = false, $raw = false)
    {

        if (is_callable(self::$dbPassword))  {
            self::$dbPassword = call_user_func(self::$dbPassword);
        }

        if (empty(self::$dbPassword)) {
            self::$dbPassword = '-guikeyprompt';
        } else {
            self::$dbPassword =  sprintf('-pw:\'%s\'',self::$dbPassword);
        }

        $command = $this->buildCommand();

        if ($debug === true) {
            echo "Running: \n$command\n";
        }

        $process = new Process($command);
        $process->run();

        if ( $process->getExitCode() === 0 && !preg_match('/E:\s.*/',$process->getOutput()) ) {
            return ($raw) ? $process->getOutput() : trim(preg_replace('/OK: Operation completed successfully./','',$process->getOutput()));
        } else {
            throw new \KeePass\Exceptions\KeePassScriptErrorException($process);
        }

    }

}
