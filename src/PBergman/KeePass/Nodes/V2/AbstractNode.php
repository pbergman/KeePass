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

    /**
     * @param \DomNode   $element
     * @param \DOMDocument  $dom
     * @param bool          $validate
     */
    function __construct(\DomNode $element = null, \DOMDocument $dom = null, $validate = true)
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
     * should return array of properties of the dom
     * that can be accessed by the __call method,
     *
     * @return array
     */
    abstract protected function getProperties();

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
        if (false !== $schema = $this->getValidateSchema()) {
            set_error_handler(function($type, $message, $file, $line, $stack){
                trigger_error($message, E_USER_ERROR);
            }, E_WARNING);

            $doc = new \DOMDocument('1.0', 'UTF-8');
            $doc->appendChild($doc->importNode($this->element, true));
            $doc->schemaValidateSource($schema);
            restore_error_handler();
        }
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

    /**
     * will create type based on value
     *
     * @param   $var
     * @return  string
     */
    protected function unStringify($var)
    {
        if (is_string($var)) {
            switch (strtolower($var)) {
                case 'false':
                case 'true':
                    return strtolower($var) === 'true' ? true : false;
                    break;
                case 'null':
                    return null;
                    break;
            }
        }
        return $var;
    }

    /**
     * @param   string      $name
     * @param   array       $arguments
     * @return  $this|string|array
     */
    public function __call($name, $arguments)
    {

        if (preg_match('#^(?P<method>get|set)(?P<name>.+)$#', $name, $ret)) {

            $element = $this->element->getElementsByTagName($ret['name']);

            if (in_array($ret['name'], $this->getProperties()) && $element->length > 0) {

                switch ($ret['method']) {
                    case 'get':
                        if ($element->length > 1) {
                            $ret = [];
                            /** @var \DomElement $e */
                            foreach ($element as $e) {
                                $ret[] = $this->unStringify($e->textContent);
                            }
                            return $ret;
                        } else {
                            return $this->unStringify($element->item(0)->textContent);
                        }
                        break;
                    case 'set':
                        if ($element->length > 1) {
                            throw new \RuntimeException('Trying to set value to a element that represent multiple elements');
                        } else {
                            $this->element->getElementsByTagName($ret['name'])->item(0)->textContent = $this->stringify($arguments[0]);
                            return $this;
                        }
                        break;
                }
            }  else {
                throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));
            }

        } else {
            throw new \RuntimeException(sprintf('Calling to undefined method: "%s"', $name));
        }
    }
}