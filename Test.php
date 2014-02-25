<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 */

require_once 'vendor/autoload.php';


$container = new \KeePass\Application(
    function(){
        return readline('Keepass database password: ');
    }
);

// Run directly from kps
echo $container->get('keepass')
               ->getKpScript()
               ->get('GenPw')->setCount(100)->run(true);exit;

echo $container->get('keepass')
               ->getKpScript()
               ->get('GetEntryString')
               ->setField('Password')
               ->setRef('UUID','3F64A5B641F6F84E84BE05B35A2B8E7E')
               ->run(true);


// Remove cache
/** @var KeePass\EntityController\Controller $ec */
/*
$ec = $container->get('entity_controller');
$ec->removeCache(true,true);exit;
*/



///** @var \KeePass\KeePass $kp */
//$kp     = $container->get('keepass');
//$ec     = $kp->getEntityController();
///** @var KeePass\EntityController\Filters\Group $groups */
//$groups  = $ec->getEntities('group');
//$result  = $groups
//             ->where('name','Z%', 'like')
//             ->getEntries()
//             ->whereInData('url','%zicht.nl','like')
//             ->getGroup()
//             ->getResult();
//
//// Get entries from group matching pattern
////$result  = $groups
////    ->where('name','Z5')
////    ->getEntries()
////    ->whereInData('url', '%zicht.nl', 'like')
////    ->getResult();
//
//var_dump($result);
