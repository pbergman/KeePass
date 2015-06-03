<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\KeePass\Nodes\V2;

use PBergman\KeePass\Nodes\V2\Entities\Entry;

class QueryBuilder
{
    const SEARCH_ALL = 1;
    const SEARCH_HISTORY = 2;
    const SEARCH_ENTRY = 3;
    const SEARCH_GROUP = 4;

    const ELEMENT_ALL = '*/';
    const ELEMENT_STRING_KEY = 'String/Key/';
    const ELEMENT_STRING_VALUE = 'String/Value/';

    protected $search = self::SEARCH_ALL;
    protected $where;
    protected $node;

    function __construct(Node $node)
    {
        $this->node = $node;
    }


    /**
     * set where to search in
     *
     * @param   int $search
     * @return  $this
     */
    public function searchIn($search = self::SEARCH_ALL)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * add where statement
     *
     * @param   string    $value
     * @param   string    $element
     * @return  $this
     */
    public function where($value, $element = self::ELEMENT_ALL)
    {
        $this->where = [$value, $element];
        return $this;
    }

    public function getQuery()
    {
        $query = null;

        switch ($this->search) {
            case self::SEARCH_ALL:
                $query[] = '//';
                break;
            case self::SEARCH_ENTRY:
                $query[] = '//Group/Entry/';
                break;
            case self::SEARCH_GROUP:
                $query[] = '//Group/Entry/../';
                break;
            case self::SEARCH_HISTORY:
                $query[] = '//History/';
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported "search in", %s', $this->search));
                break;
        }

        list($value, $element) = $this->where;

        $query[] = sprintf('%s[text()="%s"]', $element, $value);

        return implode('', $query);
    }

    public function search()
    {
        $results = $this->node->getXpath()->query($this->getQuery());
        $return = [];

        if ($results->length > 0) {
            /** @var \DOMElement $result */
            foreach ($results as $result) {
                while ($result->tagName !== 'Entry') {
                    $result = $this->node->getXpath()->query($this->getQuery() . '/..')->item(0);
                }

                $return[] = new Entry($result);
            }

            return $return;
        } else {
            return null;
        }
    }
}