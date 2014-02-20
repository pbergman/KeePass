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
/**
echo $container->get('keepass')->getKpScript()->get('list_groups')->run();
*/

// Remove cache
/** @var KeePass\EntityController\Controller $ec */
/*
$ec = $container->get('entity_controller');
$ec->removeCache(true,true);exit;
*/



/** @var \KeePass\KeePass $kp */
$kp     = $container->get('keepass');
$ec     = $kp->getEntityController();
/** @var KeePass\EntityController\Filters\Group $groups */
$groups  = $ec->getEntities('group');
$result  = $groups
             ->where('name','Z%', 'like')
             ->getEntries()
             ->whereInData('url','%zicht.nl','like')
             ->getGroup()
             ->getResult();

// Get entries from group matching pattern
//$result  = $groups
//    ->where('name','Z5')
//    ->getEntries()
//    ->whereInData('url', '%zicht.nl', 'like')
//    ->getResult();

var_dump($result);
