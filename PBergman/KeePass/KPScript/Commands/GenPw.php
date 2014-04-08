<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace PBergman\KeePass\KPScript\Commands;

/**
 * Class GenPw
 *
 * @package PBergman\KeePass\KPScript\Commands
 */
class GenPw extends BasCommand
{

    const PROFILE_40_BIT        = 0;
    const PROFILE_128_BIT       = 1;
    const PROFILE_256_BIT       = 2;
    const PROFILE_RANDOM_MAC    = 3;
    const PROFILE_RANDOM_STRING = 4;

    /** @var array  */
    protected $profiles = array(
        self::PROFILE_40_BIT        => '40-Bit Hex Key (built-in)',
        self::PROFILE_128_BIT       => '128-Bit Hex Key (built-in)',
        self::PROFILE_256_BIT       => '256-Bit Hex Key (built-in)',
        self::PROFILE_RANDOM_MAC    => 'Random MAC Address (built-in)',
        self::PROFILE_RANDOM_STRING => 'Automatically generated password for new entries',
    );
    /** @var string  */
    protected $command  = "GenPw";
    /** @var bool|int  */
    protected $count    = false;
    /** @var int|string  */
    protected $profile  = 'Automatically generated password for new entries';

    /**
     * will build reference string for command
     *
     * @return string
     * @throws \Exception
     */
    public function buildCommand()
    {
        if ( empty($this->profile) ) {
            throw new \Exception('Need a valid profile');
        }

        return sprintf(
            '%s %s -c:%s %s -profile:"%s"',
            self::$mono,
            self::$kpsLocation,
            $this->command,
            $this->getCount(),
            $this->profile
        );
    }

    /**
     * will return count option or null if not set
     *
     * @return string|null
     */
    public function getCount()
    {
        if ( $this->count === false) {
            $return = null;
        } else {
            $return = sprintf("-count:%s", $this->count);
        }

        return $return;
    }

    /**
     * set the amount of passwords to generate
     *
     * @param  int      $count
     * @return object   $this
     * @throws \Exception
     */
    public function setCount($count)
    {
        if (is_numeric($count)) {
            $this->count = $count;
        }else {
            throw new \Exception('Only numbers can be set');
        }

        return $this;
    }

    /**
     * setting profile for generating password
     *
     * @param   string $profile    password generate profile
     * @return  object $this
     * @throws \PBergman\KeePass\Exceptions\OptionNotAllowedException
     */
    public function setProfile($profile)
    {
        if (is_numeric($profile)) {

            if (isset($this->profiles[$profile])) {
                $profile = $this->profiles[$profile];
            } else {
                throw new \PBergman\KeePass\Exceptions\OptionNotAllowedException($profile, array_keys($this->profiles));
            }

        } else {

            if (!in_array($profile, $this->profiles)) {
                throw new \PBergman\KeePass\Exceptions\OptionNotAllowedException($profile, $this->profiles);
            }

        }

        $this->profile = $profile;

        return $this;
    }
}