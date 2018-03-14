<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Encoding\BooleanEncoding;
use PBergman\KeePass\Encoding\DateTimeEncoding;
use PBergman\KeePass\Node\Meta;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlElementRootInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlParserState;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class MetaElementParser
 *
 * @package PBergman\KeePass\Parser\Element
 */
class MetaElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementRootInterface
{
    /** @var Meta */
    protected $node;
    /** @var XmlPathInterface */
    protected $xpath;

    /**
     * MetaParser constructor.
     *
     * @param Meta $node
     */
    public function __construct(Meta $node)
    {
        $this->node = $node;
    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'Meta';
    }

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        if (!empty($data)) {
            switch ($this->xpath->current()) {
                case 'DatabaseNameChanged':
                case 'DatabaseDescriptionChanged':
                case 'DefaultUserNameChanged':
                case 'MasterKeyChanged':
                case 'RecycleBinChanged':
                case 'EntryTemplatesGroupChanged':
                    $data = DateTimeEncoding::unmarshal($data);
                    break;
                case 'MaintenanceHistoryDays':
                case 'MasterKeyChangeRec':
                case 'MasterKeyChangeForce':
                case 'HistoryMaxItems':
                case 'HistoryMaxSize':
                    $data = (int)$data;
                    break;
                case 'RecycleBinEnabled':
                    $data = BooleanEncoding::unmarshal($data);
                    break;
                case 'CustomIcons':
                case 'Binaries':
                case 'MemoryProtection':
                    return XmlParserState::STATE_FORWARD_CHILD;
            }

            $method = 'set' . $this->xpath->current();

            if (method_exists($this->node, $method)) {
                $this->node->{$method}($data);
            }
        }

        return XmlParserState::STATE_FEED;
    }

    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return XmlParserState::STATE_FINISHED;
    }

    /**
     * @param XmlElementParserInterface $child
     */
    public function initChild(XmlElementParserInterface $child)
    {
        switch (true) {
            case $child instanceof CustomIconsElementParser:
                $child->setContext($this->node->getCustomIcons());
                break;
            case $child instanceof BinariesElementParser:
                $child->setContext($this->node->getBinaries());
                break;
            case $child instanceof MemoryProtectionElementParser:
                $child->setContext($this->node->getMemoryProtection());
                break;
        }
    }
}