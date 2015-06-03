<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */


require_once 'vendor/autoload.php';

$k    = new \PBergman\KeePass\KeePass();
$node = $k->loadFile('**', '***');
$node->decrypt();
$q = $node->getQueryBuilder();
$r = $q
    ->searchIn($q::SEARCH_ENTRY)
    ->where('B9TD4z4lg9eLn6K0wEpLQQ==','UUID')
    ->search();
/** @var \PBergman\KeePass\Nodes\V2\Entities\Entry $a */
foreach ($r as $a) {
    var_dump($a);
}

exit;
$group = new \PBergman\KeePass\Nodes\V2\Entities\Group();
$times = new \PBergman\KeePass\Nodes\V2\Entities\Times();
$times->setLocationChanged(new DateTime('+4 days'));
$group->setTimes($times);
echo $group;exit;



