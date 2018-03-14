<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Node;

use PBergman\KeePass\Model\ArrayCollection;
use PBergman\KeePass\Model\BinaryString;

class Group
{
    /** @var BinaryString */
    private $uuid;
    /** @var string */
    private $name;
    /** @var string */
    private $note;
    /** @var Times */
    private $times;
    /** @var int */
    private $iconID;
    /** @var bool */
    private $isExpanded;
    /** @var bool|null */
    private $enableAutoType;
    /** @var bool|null */
    private $enableSearching;
    /** @var BinaryString */
    private $lastTopVisibleEntry;
    /** @var ArrayCollection */
    private $groups;
    /** @var ArrayCollection */
    private $entries;

    /**
     * Group constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    /**
     * @return BinaryString
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * @param BinaryString $uuid
     */
    public function setUUID(BinaryString $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return Times
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param Times $times
     * @return Times
     */
    public function setTimes($times)
    {
        $this->times = $times;
        return $times;
    }

    /**
     * @return int
     */
    public function getIconID()
    {
        return $this->iconID;
    }

    /**
     * @param int $iconID
     */
    public function setIconID($iconID)
    {
        $this->iconID = $iconID;
    }

    /**
     * @return bool
     */
    public function isExpanded()
    {
        return $this->isExpanded;
    }

    /**
     * @param bool $isExpanded
     */
    public function setIsExpanded($isExpanded)
    {
        $this->isExpanded = $isExpanded;
    }

    /**
     * @return bool|null
     */
    public function getEnableAutoType()
    {
        return $this->enableAutoType;
    }

    /**
     * @param bool|null $enableAutoType
     */
    public function setEnableAutoType($enableAutoType)
    {
        $this->enableAutoType = $enableAutoType;
    }

    /**
     * @return bool|null
     */
    public function getEnableSearching()
    {
        return $this->enableSearching;
    }

    /**
     * @param bool|null $enableSearching
     */
    public function setEnableSearching($enableSearching)
    {
        $this->enableSearching = $enableSearching;
    }

    /**
     * @return BinaryString
     */
    public function getLastTopVisibleEntry()
    {
        return $this->lastTopVisibleEntry;
    }

    /**
     * @param BinaryString $lastTopVisibleEntry
     */
    public function setLastTopVisibleEntry(BinaryString $lastTopVisibleEntry)
    {
        $this->lastTopVisibleEntry = $lastTopVisibleEntry;
    }

    /**
     * @return ArrayCollection|Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param ArrayCollection|Group[] $groups
     */
    public function setGroups($groups)
    {
        $this->groups->reset();
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    /**
     * @param Group $group
     * @return Group
     */
    public function addGroup(Group $group)
    {
        if (!$this->groups->has($group)) {
            $this->groups[] = $group;
        }
        return $group;
    }

    /**
     * @return ArrayCollection|Entry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param ArrayCollection|Entry[] $entries)
     */
    public function setEntries($entries)
    {
        $this->entries->reset();
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }
    }

    /**
     * @param Entry $entry
     * @return Entry
     */
    public function addEntry(Entry $entry)
    {
        if (!$this->entries->has($entry)) {
            $this->entries[] = $entry;
        }
        return $entry;
    }
}
