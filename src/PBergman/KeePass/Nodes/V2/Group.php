<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */

namespace PBergman\KeePass\Nodes\V2;

/**
 * Class String
 *
 * @package PBergman\KeePass\Nodes\V2
 *
 */
class Group extends AbstractNode
{

    /**
     * returns the default xml that specifies this node
     *
     * @return \SimpleXMLElement
     */
    protected function getDefaultElement()
    {
        $element = new \SimpleXMLElement('<Group />');
        $element->addChild('UUID');
        $element->addChild('Name');
        $element->addChild('Notes');
        $element->addChild('Times');
        $element->addChild('IsExpanded');
        $element->addChild('DefaultAutoTypeSequence');
        $element->addChild('EnableAutoType');
        $element->addChild('LastTopVisibleEntry');

        $dom = new \DOMDocument();
        $group = $dom->createElement('group');
        var_dump($group, dom_import_simplexml($element));exit;
        $dom->appendChild();
        var_dump($dom->C14N());exit;

//        $dom = dom_import_simplexml($element);
//        $dom->create
//        $dom->appendChild((new \DOMNode())->nodeName = 'foo');
//        $uuid = new \DOMElement('UUID');
//        $name = new \DOMElement('Name');
//        $notes = new \DOMElement('Notes');
//        $times = new \DOMElement('Times');
//        $times->
//        $uuid = new \DOMElement('IsExpanded');
//        $uuid = new \DOMElement('DefaultAutoTypeSequence');
//        $uuid = new \DOMElement('EnableAutoType');
//        $uuid = new \DOMElement('LastTopVisibleEntry');
//

        return $element;

    }
}

//<UUID>HyAro1dG50GtpHNstre0dA==</UUID>
//<Name>Accounts</Name>
//<Notes />
//<IconID>58</IconID>
//<Times>
//    <CreationTime>2013-09-18T10:18:27Z</CreationTime>
//    <LastModificationTime>2013-09-18T10:18:58Z</LastModificationTime>
//    <LastAccessTime>2015-03-03T13:38:02Z</LastAccessTime>
//    <ExpiryTime>2013-09-17T22:00:00Z</ExpiryTime>
//    <Expires>False</Expires>
//    <UsageCount>34</UsageCount>
//    <LocationChanged>2013-09-18T10:19:05Z</LocationChanged>
//</Times>
//<IsExpanded>True</IsExpanded>
//<DefaultAutoTypeSequence />
//<EnableAutoType>null</EnableAutoType>
//<EnableSearching>null</EnableSearching>
//<LastTopVisibleEntry>ysecpf47WU6+dtIFOPxVBQ==</LastTopVisibleEntry>