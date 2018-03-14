<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Model;

class XmlIndex implements \Serializable
{
    /** @var int  */
    protected $metaOffsetStart = 0;
    /** @var int  */
    protected $metaOffsetEnd = 0;
    /** @var  array|XmIndexElement[] */
    protected $groups = [];
    /** @var  array|XmIndexElement[] */
    protected $entries = [];

    /**
     * @return int
     */
    public function getMetaLength()
    {
        return $this->metaOffsetEnd - $this->metaOffsetStart;
    }

    /**
     * @return int[]
     */
    public function getMetaOffset()
    {
        return [$this->metaOffsetStart, $this->getMetaLength()];
    }

    /**
     * @param int $start
     * @param int $end
     */
    public function setMetaOffset($start, $end)
    {
        $this->setMetaOffsetStart($start);
        $this->setMetaOffsetEnd($end);
    }

    /**
     * @param int  $start
     */
    public function setMetaOffsetStart($start)
    {
        $this->metaOffsetStart = $start;
    }

    /**
     * @param int $end
     */
    public function setMetaOffsetEnd($end)
    {
        $this->metaOffsetEnd = $end;
    }

    /**
     * @return array|XmIndexElement[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array|XmIndexElement[] $groups
     */
    public function setGroups(...$groups)
    {
        $this->groups = [];
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    /**
     * @param XmIndexElement $element
     */
    public function addGroup(XmIndexElement $element)
    {
        if (!in_array($element, $this->groups, true)) {
            $this->groups[] = $element;
        }
    }

    /**
     * @return array|XmIndexElement[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param array|XmIndexElement[] $entries
     */
    public function setEntries($entries)
    {
        $this->entries = [];
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
    }

    /**
     * @param XmIndexElement $element
     */
    public function addEntry(XmIndexElement $element)
    {
        if (!in_array($element, $this->entries, true)) {
            $this->entries[] = $element;
        }
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->metaOffsetStart,
            $this->metaOffsetEnd,
            $this->groups,
            $this->entries,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->metaOffsetStart,
            $this->metaOffsetEnd,
            $this->groups,
            $this->entries,
        ) = unserialize($serialized);
    }
}