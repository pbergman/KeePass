<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */
namespace KeePass;

use \Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;
use \Symfony\Component\Filesystem\Exception\FileNotFoundException;
use \Symfony\Component\Yaml\Yaml;
use \Symfony\Component\Config\Definition\Processor;

/**
 * Class    ConfigLoader
 * @package KeePass
 */
class ConfigLoader extends BaseFileLoader
{
    public function load($resource, $type = null)
    {
        $config    = array('KeePass' => Yaml::parse($resource));
        $processor = new Processor();

        $return = $processor->processConfiguration(
            new Configuration(),
            $config
        );

        /**
         * Replace %VARS% with environment vars
         */
        array_walk_recursive($return,function (&$item) {
            preg_match_all('/(?P<SEARCH>%(?P<REPLACE>[^%]+)%)/',$item,$ret);

            $replace  = true;
            $replace &= !empty($ret['SEARCH']);
            $replace &= !empty($ret['REPLACE']);

            if ($replace == true) {
                array_walk($ret['SEARCH'],function (&$var) {
                    $var = sprintf('/%s/',$var);
                });

                array_walk($ret['REPLACE'],function (&$var) {
                    $var = sprintf('%s',getenv($var));
                });

                $item = preg_replace($ret['SEARCH'],$ret['REPLACE'],$item);
            }

        });

        /**
         * Replace @VARS@ with internal vars
         */
        array_walk_recursive($return,function (&$item) use ($return) {

            if ( preg_match_all('/(?P<SEARCH>@(?P<REPLACE>[^@]+)@)/',$item,$ret) ) {

                $replaces   = array();

                foreach ($ret['REPLACE'] as $replace) {

                    $arrayPath = explode('.',$replace);
                    $found     = false;

                    foreach ($arrayPath as $p) {

                        if ($found === false) {
                            if (isset($return[$p])) {
                                $found = $return[$p];
                            } else {
                                $found = $replace;
                                break;
                            }
                        } else {
                            if (isset($found[$p])) {
                                $found = $found[$p];
                            } else {
                                $found = $replace;
                                break;
                            }
                        }

                    }

                    $replaces[] = $found;
                }

                array_walk($ret['SEARCH'],function (&$var) {
                    $var = sprintf('/%s/',$var);
                });

                $item = preg_replace($ret['SEARCH'], $replaces, $item);
            }

        });

        $this->checkKeePassConfig($return);
        $GLOBALS['OPTIONS'] = $return;

        return $return;

    }

    private function checkKeePassConfig($config)
    {
        foreach ($config['keepass'] as $key => $value) {
            if (!file_exists($value)) {
                throw new FileNotFoundException(
                    sprintf(
                        '[config][%s] File "%s" could not be found.',
                        $key,
                        $value
                    )
                );
            }
        }

        $haveMonoComplete = trim(`dpkg -l | grep mono-complete | awk '{print $3}'`);

        if (empty($haveMonoComplete)) {
            throw new FileNotFoundException('Mono-complete not found, install mono-complete first!');
        }

    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
