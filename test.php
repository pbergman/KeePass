<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
//print join '', map {rand 256} 1..16


require_once 'vendor/autoload.php';


$k = new PBergman\KeePass\KeePass();
$r = $k->loadFile('FILE', 'PASSWORD');
echo $r;
