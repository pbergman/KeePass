<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Model;

class XmIndexElement implements \Serializable
{
    /** @var int  */
    protected $offsetStart = 0;
    /** @var int  */
    protected $offsetEnd = 0;
    /** @var string */
    protected $name;
    /** @var string  */
    protected $id;
    /** @var bool  */
    protected $isHistory;

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->offsetEnd - $this->offsetStart;
    }

    /**
     * @return int[]
     */
    public function getOffset()
    {
        return [$this->offsetStart, $this->getLength()];
    }

    /**
     * @param int $start
     * @param int $end
     */
    public function setOffset($start, $end)
    {
        $this->setOffsetStart($start);
        $this->setOffsetEnd($end);
    }

    /**
     * @param int  $start
     */
    public function setOffsetStart($start)
    {
        $this->offsetStart = $start;
    }

    /**
     * @param int $end
     */
    public function setOffsetEnd($end)
    {
        $this->offsetEnd = $end;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isHistory()
    {
        return $this->isHistory;
    }

    /**
     * @param bool $isHistory
     */
    public function setIsHistory($isHistory)
    {
        $this->isHistory = $isHistory;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->offsetStart,
            $this->offsetEnd,
            $this->name,
            $this->id,
            (int)$this->isHistory
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->offsetStart,
            $this->offsetEnd,
            $this->name,
            $this->id,
            $this->isHistory
        ) = unserialize($serialized);

        $this->isHistory = (bool)$this->isHistory;
    }
}