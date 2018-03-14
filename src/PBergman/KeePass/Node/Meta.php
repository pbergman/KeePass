<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Node;

use PBergman\KeePass\Model\ArrayCollection;

class Meta
{
    /** @var string */
    private $generator;
    /** @var string */
    private $headerHash;
    /** @var string */
    private $databaseName;
    /** @var \DateTimeInterface */
    private $databaseNameChanged;
    /** @var string */
    private $databaseDescription;
    /** @var \DateTimeInterface */
    private $databaseDescriptionChanged;
    /** @var string */
    private $defaultUserName;
    /** @var \DateTimeInterface */
    private $defaultUserNameChanged;
    /** @var int */
    private $maintenanceHistoryDays;
    /** @var \DateTimeInterface */
    private $masterKeyChanged;
    /** @var int */
    private $masterKeyChangeRec;
    /** @var int */
    private $masterKeyChangeForce;
    /** @var MemoryProtection */
    private $memoryProtection;
    /** @var ArrayCollection|CustomIcon[] */
    private $customIcons;
    /** @var bool */
    private $recycleBinEnabled;
    /** @var string */
    private $recycleBinUUID;
    /** @var \DateTimeInterface */
    private $recycleBinChanged;
    /** @var string */
    private $entryTemplatesGroup;
    /** @var \DateTimeInterface */
    private $entryTemplatesGroupChanged;
    /** @var int */
    private $historyMaxItems;
    /** @var int */
    private $historyMaxSize;
    /** @var string */
    private $lastSelectedGroup;
    /** @var string */
    private $lastTopVisibleGroup;
    /** @var ArrayCollection|Binary[] */
    private $binaries;
    /** @var string */
    private $color;
    //private $customData

    public function __construct()
    {
        $this->binaries = new ArrayCollection();
        $this->customIcons = new ArrayCollection();
        $this->memoryProtection = new MemoryProtection();
    }

    /**
     * @return string
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @param string $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }
    /**
     * @return string
     */
    public function getHeaderHash()
    {
        return $this->headerHash;
    }

    /**
     * @param string $headerHash
     */
    public function setHeaderHash($headerHash)
    {
        $this->headerHash = $headerHash;
    }

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @param string $databaseName
     */
    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDatabaseNameChanged()
    {
        return $this->databaseNameChanged;
    }

    /**
     * @param \DateTimeInterface $databaseNameChanged
     */
    public function setDatabaseNameChanged($databaseNameChanged)
    {
        $this->databaseNameChanged = $databaseNameChanged;
    }

    /**
     * @return string
     */
    public function getDatabaseDescription()
    {
        return $this->databaseDescription;
    }

    /**
     * @param string $databaseDescription
     */
    public function setDatabaseDescription($databaseDescription)
    {
        $this->databaseDescription = $databaseDescription;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDatabaseDescriptionChanged()
    {
        return $this->databaseDescriptionChanged;
    }

    /**
     * @param \DateTimeInterface $databaseDescriptionChanged
     */
    public function setDatabaseDescriptionChanged($databaseDescriptionChanged)
    {
        $this->databaseDescriptionChanged = $databaseDescriptionChanged;
    }

    /**
     * @return string
     */
    public function getDefaultUserName()
    {
        return $this->defaultUserName;
    }

    /**
     * @param string $defaultUserName
     */
    public function setDefaultUserName($defaultUserName)
    {
        $this->defaultUserName = $defaultUserName;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDefaultUserNameChanged()
    {
        return $this->defaultUserNameChanged;
    }

    /**
     * @param \DateTimeInterface $defaultUserNameChanged
     */
    public function setDefaultUserNameChanged($defaultUserNameChanged)
    {
        $this->defaultUserNameChanged = $defaultUserNameChanged;
    }

    /**
     * @return int
     */
    public function getMaintenanceHistoryDays()
    {
        return $this->maintenanceHistoryDays;
    }

    /**
     * @param int $maintenanceHistoryDays
     */
    public function setMaintenanceHistoryDays($maintenanceHistoryDays)
    {
        $this->maintenanceHistoryDays = $maintenanceHistoryDays;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getMasterKeyChanged()
    {
        return $this->masterKeyChanged;
    }

    /**
     * @param \DateTimeInterface $masterKeyChanged
     */
    public function setMasterKeyChanged($masterKeyChanged)
    {
        $this->masterKeyChanged = $masterKeyChanged;
    }

    /**
     * @return int
     */
    public function getMasterKeyChangeRec()
    {
        return $this->masterKeyChangeRec;
    }

    /**
     * @param int $masterKeyChangeRec
     */
    public function setMasterKeyChangeRec($masterKeyChangeRec)
    {
        $this->masterKeyChangeRec = $masterKeyChangeRec;
    }

    /**
     * @return int
     */
    public function getMasterKeyChangeForce()
    {
        return $this->masterKeyChangeForce;
    }

    /**
     * @param int $masterKeyChangeForce
     */
    public function setMasterKeyChangeForce($masterKeyChangeForce)
    {
        $this->masterKeyChangeForce = $masterKeyChangeForce;
    }

    /**
     * @return MemoryProtection
     */
    public function getMemoryProtection()
    {
        return $this->memoryProtection;
    }

    /**
     * @param MemoryProtection $memoryProtection
     */
    public function setMemoryProtection($memoryProtection)
    {
        $this->memoryProtection = $memoryProtection;
    }

    /**
     * @return ArrayCollection|CustomIcon[]
     */
    public function getCustomIcons()
    {
        return $this->customIcons;
    }

    /**
     * @param ArrayCollection|CustomIcon[] $customIcons
     */
    public function setCustomIcons($customIcons)
    {
        $this->customIcons->reset();
        foreach ($customIcons as $customIcon) {
            $this->addCustomIcon($customIcon);
        }
    }

    /**
     * @param CustomIcon $customIcon
     */
    public function addCustomIcon(CustomIcon $customIcon)
    {
        if (!$this->customIcons->has($customIcon)) {
            $this->customIcons[] = $customIcon;
        }
    }

    /**
     * @return bool
     */
    public function isRecycleBinEnabled()
    {
        return $this->recycleBinEnabled;
    }

    /**
     * @param bool $recycleBinEnabled
     */
    public function setRecycleBinEnabled($recycleBinEnabled)
    {
        $this->recycleBinEnabled = $recycleBinEnabled;
    }

    /**
     * @return string
     */
    public function getRecycleBinUUID()
    {
        return $this->recycleBinUUID;
    }

    /**
     * @param string $recycleBinUUID
     */
    public function setRecycleBinUUID($recycleBinUUID)
    {
        $this->recycleBinUUID = $recycleBinUUID;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getRecycleBinChanged()
    {
        return $this->recycleBinChanged;
    }

    /**
     * @param \DateTimeInterface $recycleBinChanged
     */
    public function setRecycleBinChanged($recycleBinChanged)
    {
        $this->recycleBinChanged = $recycleBinChanged;
    }

    /**
     * @return string
     */
    public function getEntryTemplatesGroup()
    {
        return $this->entryTemplatesGroup;
    }

    /**
     * @param string $entryTemplatesGroup
     */
    public function setEntryTemplatesGroup($entryTemplatesGroup)
    {
        $this->entryTemplatesGroup = $entryTemplatesGroup;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getEntryTemplatesGroupChanged()
    {
        return $this->entryTemplatesGroupChanged;
    }

    /**
     * @param \DateTimeInterface $entryTemplatesGroupChanged
     */
    public function setEntryTemplatesGroupChanged($entryTemplatesGroupChanged)
    {
        $this->entryTemplatesGroupChanged = $entryTemplatesGroupChanged;
    }

    /**
     * @return int
     */
    public function getHistoryMaxItems()
    {
        return $this->historyMaxItems;
    }

    /**
     * @param int $historyMaxItems
     */
    public function setHistoryMaxItems($historyMaxItems)
    {
        $this->historyMaxItems = $historyMaxItems;
    }

    /**
     * @return int
     */
    public function getHistoryMaxSize()
    {
        return $this->historyMaxSize;
    }

    /**
     * @param int $historyMaxSize
     */
    public function setHistoryMaxSize($historyMaxSize)
    {
        $this->historyMaxSize = $historyMaxSize;
    }

    /**
     * @return string
     */
    public function getLastSelectedGroup()
    {
        return $this->lastSelectedGroup;
    }

    /**
     * @param string $lastSelectedGroup
     */
    public function setLastSelectedGroup($lastSelectedGroup)
    {
        $this->lastSelectedGroup = $lastSelectedGroup;
    }

    /**
     * @return string
     */
    public function getLastTopVisibleGroup()
    {
        return $this->lastTopVisibleGroup;
    }

    /**
     * @param string $lastTopVisibleGroup
     */
    public function setLastTopVisibleGroup($lastTopVisibleGroup)
    {
        $this->lastTopVisibleGroup = $lastTopVisibleGroup;
    }

    /**
     * @return ArrayCollection|Binary[]
     */
    public function getBinaries()
    {
        return $this->binaries;
    }

    /**
     * @param Binary[] $binaries
     */
    public function setBinaries(Binary... $binaries)
    {
        $this->binaries->reset();
        foreach ($binaries as $binary) {
            $this->addBinary($binary);
        }
    }


    /**
     * @param Binary $binary
     */
    public function addBinary($binary)
    {
        if (!$this->binaries->has($binary)) {
            $this->binaries[] = $binary;
        }
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
}