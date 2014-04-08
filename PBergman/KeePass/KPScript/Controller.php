<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\KPScript;

/**
 * Class    Controller
 * @package PBergman\KeePass\KPScript
 */
class Controller
{
    private $mono;
    private $kpsLocation;
    private $dbLocation;
    private $dbPassword;

    /**
     * constructor will set some variables to build the kps base command
     *
     * @param $dbLocation
     * @param $dbPassword
     * @param $kpsLocation
     * @param $mono
     */
    public function __construct($dbLocation, $dbPassword, $kpsLocation, $mono)
    {
        Commands\BasCommand::setMono($mono);
        Commands\BasCommand::setDbLocation($dbLocation);
        Commands\BasCommand::setKpsLocation($kpsLocation);
        Commands\BasCommand::setDbPassword($dbPassword);

    }

    /**
     *
     * @param  $name
     * @return bool
     * @throws \Exception
     */
    public function get($name)
    {
        $className =  sprintf(
            __NAMESPACE__ . "\\Commands\\%s",
            implode('',array_map('ucfirst', preg_split('/\.|_/',$name)))
        );

        $ret = false;

        if (class_exists($className)) {

            $classRef = new \ReflectionClass($className);

            if ($classRef->isSubclassOf(__NAMESPACE__ . "\\Commands\\BasCommand")) {
                $ret = new $className;
            }
        }

        if ($ret === false) {
            throw new \Exception(sprintf('Could not find class: %s',$className));
        } else {
            return $ret;
        }
    }
}
