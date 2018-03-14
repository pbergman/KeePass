<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Node;

class MemoryProtection
{
    /** @var bool */
    private $protectTitle;
    /** @var bool */
    private $protectUserName;
    /** @var bool */
    private $protectPassword;
    /** @var bool */
    private $protectURL;
    /** @var bool */
    private $protectNotes;

    /**
     * @return bool
     */
    public function isProtectTitle()
    {
        return $this->protectTitle;
    }

    /**
     * @param bool $protectTitle
     */
    public function setProtectTitle($protectTitle)
    {
        $this->protectTitle = $protectTitle;
    }

    /**
     * @return bool
     */
    public function isProtectUserName()
    {
        return $this->protectUserName;
    }

    /**
     * @param bool $protectUserName
     */
    public function setProtectUserName($protectUserName)
    {
        $this->protectUserName = $protectUserName;
    }

    /**
     * @return bool
     */
    public function isProtectPassword()
    {
        return $this->protectPassword;
    }

    /**
     * @param bool $protectPassword
     */
    public function setProtectPassword($protectPassword)
    {
        $this->protectPassword = $protectPassword;
    }

    /**
     * @return bool
     */
    public function isProtectURL()
    {
        return $this->protectURL;
    }

    /**
     * @param bool $protectURL
     */
    public function setProtectURL($protectURL)
    {
        $this->protectURL = $protectURL;
    }

    /**
     * @return bool
     */
    public function isProtectNotes()
    {
        return $this->protectNotes;
    }

    /**
     * @param bool $protectNotes
     */
    public function setProtectNotes($protectNotes)
    {
        $this->protectNotes = $protectNotes;
    }
}