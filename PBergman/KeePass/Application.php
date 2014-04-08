<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class    Application
 *
 * @package PBergman\KeePass
 */
class Application extends ContainerBuilder
{


    /**
     * set a callback function t handle password fro KeePass database
     * if not used will do a gui prompt from keepass to set password
     *
     * @param callable $kp_password_callback
     */
    public function __construct(callable $kp_password_callback = null)
    {

        parent::__construct();

        $config = $this->getConfig();

        if ($config['keepass']['pwd'] === false && is_callable($kp_password_callback)) {
            $config['keepass']['pwd'] = $kp_password_callback;
        }


        foreach ($config as $key => $value) {

            if (is_array($value)) {

                $vars = $this->processArrayParameters($value,$key);

                foreach ($vars as  $k => $v) {
                    $this->setParameter($k, $v);
                }

            } else {
                $this->setParameter($key, $value);
            }

        }

        $loader = new XmlFileLoader($this, new FileLocator(__DIR__ .'/Config'));
        $loader->load('services.xml');
    }

    /**
     * will flatten array for config arguments so for example:
     *
     * array (
     *      'layer1' = array(
     *          'layer2' => 'text'
     *      )
     * )
     *
     *  will be accessible by  %layer1.layer2%
     *
     * @param  array  $vars
     * @param  string $key
     *
     * @return array
     */
    public function processArrayParameters(array $vars, $key)
    {
        $ret = array();

        foreach ($vars as $k => $v) {

            $subKey = sprintf("%s.%s", $key, $k);

            if (is_array($v)) {
                $ret = array_merge($ret, $this->processArrayParameters($v, $subKey));
            } else {
                $ret[$subKey] = $v;
            }

        }

        return $ret;

    }

    /**
     * initialize config
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getConfig()
    {

        $configLocation = new FileLocator(sprintf('%s/KeePass/Config',dirname(__DIR__)));
        $loader         = new ConfigLoader($configLocation);
        $configFiles    = $configLocation->locate('KeePass.yml', null, true);
        $config         = $loader->load($configFiles);

        return $config;

    }
}
