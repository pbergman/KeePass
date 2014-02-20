
#KeePass

##About
a lib that can access keepass2 database data and caches data to the shared memory

##Requirements
mono-complete
linux (only tested and made for linux env)


##Install 
With composer: 

{
    "require": {
        "pbergman/sharedmemory":       "dev-master",
        "pbergman/keepass":            "dev-master"

    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:pbergman/SHMController.git"
        },
	{
            "type": "vcs",
            "url": "git@github.com:pbergman/KeePass.git"
        }
    ]
}

##Basic configuration
There is yml file located at KeePass/Config/KeePass.yml, in here you can set the location for database to use.

##Usage

To initialize application u can use :

$application =  new \KeePass\Application();

If no password is set in config and do`nt want to use the gui you can add a callback for the passowrd, so for example:

$application = new \KeePass\Application(
    function(){
        return readline('Keepass database password: ');
    }
);

to remove all cache 

/** @var KeePass\EntityController\Controller $ec */
$ec = $application->get('entity_controller');
$ec->removeCache(true,true);exit;

to direct run kps command you can do:

echo $application->get('keepass')->getKpScript()->get('list_groups')->run();

Get entries from group matching pattern

/** @var \KeePass\KeePass $kp */
$kp     = $application->get('keepass');
$ec     = $kp->getEntityController();
/** @var KeePass\EntityController\Filters\Group $groups */
$groups  = $ec->getEntities('group');
$result  = $groups
             ->where('name','Z%', 'like')
             ->getEntries()
             ->whereInData('url','%zicht.nl','like')
             ->getGroup()
             ->getResult();

print_r($result);             
