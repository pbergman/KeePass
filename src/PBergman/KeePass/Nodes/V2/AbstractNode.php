<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

/**
 * Class AbstractNode
 *
 * @package PBergman\KeePass\Nodes\V2
 */
abstract class AbstractNode
{
    /** @var \DOMDocument  */
    protected $dom;
    /** @var \DomElement */
    protected $element;
    /** @var string */
    const ROOT_ELEMENT_NAME = null;

    /**
     * @param \DomElement $element
     */
    function __construct(\DomElement $element = null, \DOMDocument $dom = null, $validate = true)
    {
        if (!$dom) {
            $this->dom = new \DOMDocument('1.0', 'UTF-8');
            $this->dom->preserveWhiteSpace = false;
            $this->dom->formatOutput = true;
        } else {
            $this->dom = $dom;
        }

        if (!$element) {
            $this->element = $this->buildDefaultDomElement();
        } else {
            $this->element = $element;
        }

        if ($validate) {
            $this->validate();
        }
    }

    /**
     * returns the default dom node
     *
     * @return \DomElement
     */
    abstract protected function buildDefaultDomElement();

    /**
     * will return a validate schema for xml
     *
     * @return string
     */
    abstract protected function getValidateSchema();

    /**
     * returns C14N is stead of saveXml because
     * that will prefix with xml attribute
     *
     * @return string
     */
    public function __toString()
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->appendChild($doc->importNode($this->element, true));
        return $doc->saveXML();
    }

    /**
     *
     * @return  bool
     * @throws \ErrorException
     */
    protected function validate()
    {
        set_error_handler(function($type, $message, $file, $line, $stack){
            trigger_error($message, E_USER_ERROR);
        }, E_WARNING);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->appendChild($doc->importNode($this->element, true));
        $doc->schemaValidateSource($this->getValidateSchema());
        restore_error_handler();
    }

    /**
     * @return \DomElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * will create string from input type
     *
     * @param   $var
     * @return  string
     */
    protected function stringify($var)
    {
        switch (strtolower(gettype($var))) {
            case 'boolean':
                return ($var) ? 'True' : 'False';
                break;
            case 'null':
                return 'null';
                break;
            default:
                return $var;
                break;
        }
    }
}