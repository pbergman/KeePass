<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Parser\Element;

use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Node\CustomIcon;
use PBergman\KeePass\Parser\XmlElementHandlerInterface;
use PBergman\KeePass\Parser\XmlElementParserInterface;
use PBergman\KeePass\Parser\XmlDataHandlerInterface;
use PBergman\KeePass\Parser\XmlParser;
use PBergman\KeePass\Parser\XmlPathInterface;

/**
 * Class CustomIconsParser
 *
 * embedded handler for a CustomIcons element
 *
 *      <CustomIcons>
 *          <Icon>
 *              <UUID>...</UUID>
 *              <Data>...</Data>
 *          </Icon>
 *          <Icon>
 *              <UUID>...</UUID>
 *              <Data>...</Data>
 *          </Icon>
 *      </CustomIcons>
 *
 * @package PBergman\KeePass\Parser\Element
 */
class CustomIconsElementParser implements XmlElementParserInterface, XmlDataHandlerInterface, XmlElementHandlerInterface
{
    /** @var ArrayCollection */
    protected $ctx;
    /** @var XmlPathInterface  */
    protected $xpath;

    /**
     * @inheritdoc
     */
    public function onData($data)
    {
        if (empty($data) || $this->ctx->count() <= 0) {
            return;
        }

        switch ($this->xpath->current()) {
            case 'UUID':
                $this->ctx->last()->setUuid($data);
                break;
            case 'Data':
                $this->ctx->last()->setData($data);
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getElement()
    {
        return 'CustomIcons';
    }

    /**
     * @inheritdoc
     */
    public function init(XmlPathInterface $xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * @param ArrayCollection $collection
     */
    public function setContext(ArrayCollection $collection)
    {
        $this->ctx = $collection;
    }

    /**
     * @inheritdoc
     */
    public function onElementClose($parser, $name, $isClose)
    {
        if ($isClose) {
            $this->ctx = null;
        }
    }

    /**
     * @inheritdoc
     */
    public function onElementOpen($parser, $name, $attributes)
    {
        if ('Icon' === $name) {
            $this->ctx[] = new CustomIcon();
        }
    }
}