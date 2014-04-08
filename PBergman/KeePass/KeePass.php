<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass;

use PBergman\KeePass\Entity\Group;
use PBergman\KeePass\KPScript\Commands\Export;

/**
 * Class    KeePass
 *
 * @package PBergman\KeePass
 */
class KeePass
{
    /** @var null|string  */
    protected $database         = null;
    /** @var null|string  */
    protected $password         = null;
    /** @var null|string  */
    protected $kps               = null;
    /** @var string  */
    protected $mono              = '/usr/bin/mono';
    /** @var $kp_script KPScript\Controller */
    protected $kp_script         = null;
    /** @var EntityController\Controller  */
    protected $entity_controller = null;

    /**
     * will set KeePass Script controller, and build cache if needed
     */
    public function initialise()
    {

        $this->kp_script = new KPScript\Controller(
            $this->database,
            $this->password,
            $this->kps,
            $this->mono
        );

        if ( is_file($this->database) ) {

            $lastModified = filemtime($this->database);
            $ee           = $this->entity_controller;
            $shm          = $ee->getShm();

            if ( $shm->varGet('db_last_Modified') < $lastModified ) {
                $shm->varSet('db_last_Modified', $lastModified);
                $ee->removeCache(false);
                $this->buildCache();
            }

        }
    }

    /**
     * @return \PBergman\KeePass\KPScript\Controller
     */
    public function getKpScript()
    {
        return $this->kp_script;
    }

    /**
     * @param null|string $kps
     */
    public function setKps($kps)
    {
        $this->kps = $kps;
    }

    /**
     * @return null|string
     */
    public function getKps()
    {
        return $this->kps;
    }

    /**
     * @return null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param null $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getMono()
    {
        return $this->mono;
    }

    /**
     * @param string $mono
     */
    public function setMono($mono)
    {
        $this->mono = $mono;
    }

    /**
     * function to build memory cache
     */
    private function buildCache()
    {
        /** @var Export $export */
        $export = $this->kp_script->get('export');
        $export->setFormat(Export::FORMAT_KEEPASS_2_XML);
        $export->setOutput('/dev/stdout');
        $keePassExport = $export->run();

        preg_match('~(?P<XML><KeePassFile>.*</KeePassFile>).*~is', $keePassExport, $match);

        $xml     = new \SimpleXMLIterator( $match['XML'] );
        $groups  = $xml->xpath('/KeePassFile/Root/Group');

        foreach ($groups as $group) {

            $this->saveGroup($group, $this->formatName((string) $group->Name));
            $this->processGroups($group, (string) $group->Name);

        }

    }

    /**
     * will iterate through group path and safe groups and entries
     *
     * @param  \SimpleXMLIterator $path
     * @param $namespace
     * @return bool
     */
    private function processGroups(\SimpleXMLIterator $path, $namespace)
    {
        $ret     = false;
        $groups  = $path->xpath('Group');

        if ( !empty($groups) ) {

            foreach ($groups as $group) {

                $this->saveGroup($group, $this->formatName(sprintf("%s::%s", $namespace, (string) $group->Name)));

                $subGroup =  $group->xpath('Group');

                if ( !empty($subGroup) ) {

                   foreach ($subGroup  as $sub) {

                       $this->processGroups($sub, sprintf("%s::%s::%s", $namespace, (string) $group->Name, (string) $sub->Name));

                   }

                }

            }

        } else {

            $this->saveGroup($path, $namespace);

        }

        return $ret;
    }

    /**
     * will save group from given (xml) path entry
     *
     * @param \SimpleXMLIterator $path
     * @param $namespace
     */
    private function saveGroup(\SimpleXMLIterator $path, $namespace)
    {
        $entries = $this->processEntries($path, $this->formatName($namespace));
        $ec = $this->entity_controller;
        /** @var Group $eg */
        $eg = $ec->getNew('group');
        $eg->setNamespace($this->formatName($namespace));
        $eg->setUuid($this->convertUuid($path->UUID));
        $eg->setName((string) $path->Name);
        $eg->setEntries($entries);
        $eg->setCreated(new \DateTime((string) $path->Times->CreationTime));
        $eg->setLastModified(new \DateTime((string) $path->Times->LastModificationTime));

        $subGroup  = $path->xpath('Group');
        $groups    = array();

        if ( !empty($subGroup) ) {

            foreach ($subGroup  as $sub) {

                $groups[$this->convertUuid($sub->UUID)] = $this->formatName(sprintf("%s::%s", $namespace, (string) $sub->Name ));

            }

        }

        $eg->setGroups($groups);
        $ec->saveEntity($eg);
    }

    /**
     * will walk the entry xml path, and saves the entry
     *
     * @param \SimpleXMLIterator $path
     * @param $namespace
     * @return array
     */
    private function processEntries(\SimpleXMLIterator $path, $namespace)
    {
        $entries = $path->xpath('Entry');
        $return  = array();

        if ( !empty($entries) ) {

            foreach ($entries as $entry) {

                /** @var Entity\Entry $ee */
                $ec = $this->entity_controller;
                $ee = $ec->getNew('entry');
                $ee->setUuid($this->convertUuid($entry->UUID));
                $ee->setGroup($this->convertUuid($path->UUID));

                foreach ($entry->String as $string) {

                    if ($string->Key == "Title") {
                        $ee->setName((string) $string->Value);
                    } else {
                        $ee->addData($this->formatName($string->Key), (string) $string->Value );
                    }

                }

                $ee->setCreated(new \DateTime((string) $path->Times->CreationTime));
                $ee->setLastModified(new \DateTime((string) $path->Times->LastModificationTime));
                $ee->setNamespace(sprintf("%s::%s", $namespace, $this->formatName($ee->getName())));
                $ec->saveEntity($ee);

                $return[$ee->getUuid()] = $ee->getNamespace();
            }

        }

        return $return;
    }

    /**
     * concert binary id, created by keepass
     *
     * @param $uuid
     * @return string
     */
    private function convertUuid($uuid)
    {
        return strtoupper(
            bin2hex(
                base64_decode(
                    (string) $uuid
                )
            )
        );
    }

    /**
     * format name, to machine readable name
     *
     * @param $name
     * @param  bool  $preserveCase
     * @param  bool  $normalize
     * @return mixed
     */
    public function formatName($name, $preserveCase = false, $normalize = true)
    {
        if ($normalize === true) {
            $name = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($name, ENT_QUOTES, 'UTF-8'));
        }

        return preg_replace_callback(
            '/\s|\[|\]|\.|\/|\(|\)/',
            function ($match) {
                return (!in_array($match[0],array('[',']'))) ? '_' : null ;
            },
            ($preserveCase === true) ? $name : strtolower($name)
        );
    }

    /**
     * @return \PBergman\KeePass\EntityController\Controller
     */
    public function getEntityController()
    {
        return $this->entity_controller;
    }

    /**
     * @param \PBergman\KeePass\EntityController\Controller $entity_controller
     */
    public function setEntityController(\PBergman\KeePass\EntityController\Controller $entity_controller)
    {
        $this->entity_controller = $entity_controller;
    }

}
