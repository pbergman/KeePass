<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2\Traits;

use PBergman\KeePass\Nodes\V2\Entities\Times;

/**
 * Class TimesTrait
 *
 * @property \DomElement    $element
 * @property \DomDocument   dom
 *
 * @package PBergman\KeePass\Nodes\V2\Traits
 */
trait TimesTrait
{

    /**
     * @return Times
     */
    public function getTimes()
    {
        return new Times(
            $this->element->getElementsByTagName('Times')->item(0),
            $this->dom
        );
    }

    /**
     * @param   Times $times
     *
     * @return  $this
     */
    public function setTimes(Times $times)
    {
        $this->element->replaceChild(
            $this->dom->importNode($times->getElement(), true),
            $this->element->getElementsByTagName('Times')->item(0)
        );

        return $this;
    }
}