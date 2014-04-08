<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace PBergman\KeePass\KPScript\Commands;

/**
 * Class    GetEntryString
 *
 * Retrieves the value of an entry string field.
 *
 * The search filed(s) can be set with the methods
 * setRefs  set a array of ref like array('UserName' => 'admin', 'URL' => '192.168.1.0')
 *  and
 * setRef   sets ref by key value parameter
 *
 * @package PBergman\KeePass\KPScript\Commands
 */
class GetEntryString extends BasCommand
{
    /** @var string  */
    protected $command      = "GetEntryString";
    /** @var array  */
    protected $refs         = array();
    /** @var array  */
    protected $refXFields   = array('UUID', 'Tags');
    /** @var  string */
    protected $field        = 'UserName';



    /**
     * builds the command for export that will be called by run
     *
     * @return string
     * @throws \Exception
     */
    public function buildCommand()
    {
        if ( empty($this->field) || empty($this->refs) ) {
            throw new \Exception('Need to set ref/field first');
        }

        return sprintf(
            '%s %s "%s" %s -c:%s %s %s',
            self::$mono,
            self::$kpsLocation,
            self::$dbLocation,
            self::$dbPassword,
            $this->command,
            $this->getField(),
            $this->getRefs()
        );
    }

    /**
     * will build reference string for command
     *
     * @param   bool            $asArray    when set to true it will return a array in stead of string
     * @return  array|string
     * @throws \InvalidArgumentException
     */
    public function getRefs($asArray = false)
    {
        $ret = array();

        if (empty($this->refs)) {
            throw new \InvalidArgumentException('Need to set reference to earch by first!');
        } else {

            foreach ($this->refs as $key => $value) {

                // Add quotes around key/value when needed
                list($key, $value) = array_map(function($v) {

                    if (preg_match('/\s/', $v)){
                        $v = sprintf('"%s"', $v);
                    }

                    return $v;
                },array($key, $value));

                if (in_array($key,$this->refXFields)) {
                    $ret[] = sprintf('-refx-%s:%s', $key, $value);
                } else {
                    $ret[] = sprintf('-ref-%s:%s',  $key, $value);
                }
            }
        }

        return ($asArray === false) ? implode(' ', $ret) : $ret;

    }

    /**
     * set a stack of refs
     *
     * @param array $refs
     *
     * @return  $this
     */
    public function setRefs(array $refs)
    {
        $this->refs = $refs;

        return $this;
    }

    /**
     * set a single reference by key and value
     *
     * @param   $key
     * @param   $value
     *
     * @return  $this
     */
    public function setRef($key, $value)
    {
        $this->refs[$key] = $value;

        return $this;
    }

    /**
     * returns formatted field option
     *
     * @return string
     */
    public function getField()
    {

        $return = $this->field;

        if (preg_match('/\s/', $return)){
            $return = sprintf('"%s"', $return);
        }

        $return = sprintf('-Field:%s', $return);

        return $return;
    }

    /**
     * @param   $field
     *
     * @return  $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

}
