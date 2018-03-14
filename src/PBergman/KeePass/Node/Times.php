<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Node;

class Times
{
    /** @var \DateTimeInterface  */
    private $creationTime;
    /** @var \DateTimeInterface  */
    private $lastModificationTime;
    /** @var \DateTimeInterface  */
    private $lastAccessTime;
    /** @var \DateTimeInterface  */
    private $expiryTime;
    /** @var bool */
    private $expires;
    /** @var int  */
    private $usageCount;
    /** @var \DateTimeInterface  */
    private $locationChanged;

    /**
     * @return \DateTimeInterface
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @param \DateTimeInterface $creationTime
     */
    public function setCreationTime(\DateTimeInterface $creationTime)
    {
        $this->creationTime = $creationTime;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastModificationTime()
    {
        return $this->lastModificationTime;
    }

    /**
     * @param \DateTimeInterface $lastModificationTime
     */
    public function setLastModificationTime(\DateTimeInterface $lastModificationTime)
    {
        $this->lastModificationTime = $lastModificationTime;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastAccessTime()
    {
        return $this->lastAccessTime;
    }

    /**
     * @param \DateTimeInterface $lastAccessTime
     */
    public function setLastAccessTime(\DateTimeInterface $lastAccessTime)
    {
        $this->lastAccessTime = $lastAccessTime;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getExpiryTime()
    {
        return $this->expiryTime;
    }

    /**
     * @param \DateTimeInterface $expiryTime
     */
    public function setExpiryTime(\DateTimeInterface $expiryTime)
    {
        $this->expiryTime = $expiryTime;
    }

    /**
     * @return bool
     */
    public function isExpires()
    {
        return $this->expires;
    }

    /**
     * @param bool $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return int
     */
    public function getUsageCount()
    {
        return $this->usageCount;
    }

    /**
     * @param int $usageCount
     */
    public function setUsageCount($usageCount)
    {
        $this->usageCount = $usageCount;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLocationChanged()
    {
        return $this->locationChanged;
    }

    /**
     * @param \DateTimeInterface $locationChanged
     */
    public function setLocationChanged($locationChanged)
    {
        $this->locationChanged = $locationChanged;
    }
}