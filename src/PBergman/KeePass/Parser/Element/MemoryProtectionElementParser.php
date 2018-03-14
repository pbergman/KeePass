<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Encoding\BooleanEncoding;
use PBergman\KeePass\Node\MemoryProtection;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class MemoryProtectionElementParser
 *
 * embedded handler for a MemoryProtection element
 *
 * <MemoryProtection>
 *   <ProtectTitle>False</ProtectTitle>
 *   <ProtectUserName>False</ProtectUserName>
 *   <ProtectPassword>True</ProtectPassword>
 *   <ProtectURL>False</ProtectURL>
 *   <ProtectNotes>False</ProtectNotes>
 * </MemoryProtection>
 *
 * @package PBergman\KeePass\Parser
 */
class MemoryProtectionElementParser implements XmlElementParserInterface, XmlDataHandlerInterface
{
    /** @var MemoryProtection */
    protected $ctx;
    /** @var XmlPathInterface  */
    protected $xpath;

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'MemoryProtection';
    }

    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @param MemoryProtection $memoryProtection
     */
    public function setContext(MemoryProtection $memoryProtection)
    {
        $this->ctx = $memoryProtection;
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        if (!empty($data)) {
            $data = BooleanEncoding::unmarshal($data);
            switch ($this->xpath->current()) {
                case 'ProtectTitle':
                    $this->ctx->setProtectTitle($data);
                    break;
                case 'ProtectUserName':
                    $this->ctx->setProtectUserName($data);
                    break;
                case 'ProtectPassword':
                    $this->ctx->setProtectPassword($data);
                    break;
                case 'ProtectURL':
                    $this->ctx->setProtectURL($data);
                    break;
                case 'ProtectNotes':
                    $this->ctx->setProtectNotes($data);
                    break;

            }
        }
        return XmlParserState::STATE_FEED;
    }
}
