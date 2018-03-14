<?php
namespace PBergman\KeePass\Parser;

/**
 * Interface XmlDataHandlerInterface
 * 
 * @package PBergman\KeePass\Parser
 */
interface XmlDataHandlerInterface
{
    /**
     * called on data after open tag
     *
     * @param string $data
     *
     * @return int
     */
    public function onData($data);
}