<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
//print join '', map {rand 256} 1..16


require_once 'vendor/autoload.php';


$s = new \PBergman\KeePass\Nodes\V2\String();
$s->setValue('ddd');
echo $s->getValue();
$s->setKey('aaa');
var_dump($s->getElement()->asXML());
exit;

$k = new PBergman\KeePass\KeePass();




//$xml = new XMLReader();
//$xml->open('tmp.xml');
////$xml->XML($r->getContent());
//$element = null;

$data = new SimpleXMLElement($r->getContent());
$meta = $data->xpath('//*[contains(text(),"maritiemmuseum")]');
//var_dump(count($meta));exit;
foreach ($meta as $element) {

    var_dump($element->getName(), $element->registerXPathNamespace());EXIT;
//    /** @var SimpleXMLElement $attr */
//    foreach($element as $attr) {
//        var_dump((string) $attr);exit;
//    }

}
var_dump(count($meta));exit;

//while ($xml->read())
//{
//    switch ($xml->nodeType)
//    {
//        case XMLReader::END_ELEMENT:
//            if ($xml->name === $element) {
//                exit(0);
//            }
//            break;
//        case XMLReader::ELEMENT:
//            switch($xml->name) {
//                case 'Meta';
//                    var_dump(xml2assoc($xml));
//
////                    var_dump(process($xml, 'Meta'));exit;
//////                    if ($xml->isEmptyElement) {
//////                        var_dump($xml->name, $xml->value);exit;
//////                    }
////                    $element = $xml->name;
////                    var_dump($xml->name, $xml->value);;
////
////                    break;
//                default:
////                    var_dump($xml->name);
//            }
////            var_dump($xml->name);exit;
//            break;
////        case XMLReader::TEXT:
////            var_dump($xml->name, $xml->value);exit;
////            break;
//    }
//}
////function process(XMLReader $xml, $elementName)
////{
////    $nodes = [];
////    $element = null;
////
////    while($xml->read()) {
////        switch ($xml->nodeType) {
////            case XMLReader::END_ELEMENT:
////                if ($xml->name === $elementName) {
////                    break 2;
////                }
////                break;
////            case XMLReader::ELEMENT:
////
////                $element = $xml->name;
////
////                if($xml->hasAttributes){
////
////                    while($xml->moveToNextAttribute())
////                    {
////                        $nodes[$element]['attributes'][$xml->name] = $xml->value;
////                    }
////
////                }
////
////                if(!$xml->isEmptyElement) {
////                    $nodes[$element][] = process($xml, $xml->name);
////                }
////
////                break;
////            case XMLReader::TEXT:
////                return ['text' => $xml->value];
////            break;
////        }
////    }
////
////    return $nodes;
////}
//
//function xml2assoc(XMLReader $xml){
//    $elements = null;
//
//    while($xml->read()){
//        switch($xml->nodeType ) {
//            case XMLReader::END_ELEMENT:
//                break 2;
//            case XMLReader::ELEMENT:
//
//                $element = [
//                    'name'  => $xml->name,
//                    'value' => ($xml->isEmptyElement) ? null : xml2assoc($xml)
//                ];
//
//                if ($xml->hasAttributes) {
//                    while ($xml->moveToNextAttribute()) {
//                        $element['attributes'][$xml->name] = $xml->value;
//                    }
//                }
//
//                $elements[] = $element;
//
//                break;
//            case XMLReader::TEXT:
//                $elements = $xml->value;
//                break;
//        }
//    }
//
//    return $elements;
//}
////
////function xml2assoc($xml) {
////    $tree = null;
////    while($xml->read())
////        switch ($xml->nodeType) {
////            case XMLReader::END_ELEMENT: return $tree;
////            case XMLReader::ELEMENT:
////                $node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : xml2assoc($xml));
////                if($xml->hasAttributes)
////                    while($xml->moveToNextAttribute())
////                        $node['attributes'][$xml->name] = $xml->value;
////                $tree[] = $node;
////                break;
////            case XMLReader::TEXT:
////            case XMLReader::CDATA:
////                $tree .= $xml->value;
////        }
////    return $tree;
////}
//
//
//
//$xml->close();




