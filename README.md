#KeePass

##About
a lib that can access keepass2 database data and caches data to the shared memory

##Requirements
mono-complete<br>
linux (only tested and made for linux env)


##Install 
With composer: 
```
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
```
##Basic configuration
There is yml file located at KeePass/Config/KeePass.yml, in here you can set the location for database to use.

##Usage

To initialize application u can use :
```
$application =  new \KeePass\Application();
```
If no password is set in config and do`nt want to use the gui you can add a callback for the passowrd, so for example:
```
$application = new \KeePass\Application(
    function(){
        return readline('Keepass database password: ');
    }
);
```
to remove all cache 
```
/** @var KeePass\EntityController\Controller $ec */
$ec = $application->get('entity_controller');
$ec->removeCache(true,true);exit;
```
to direct run kps command you can do:
```
echo $application->get('keepass')->getKpScript()->get('list_groups')->run();
```
Get entries from group matching pattern
```
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
```
## KeePassScript

Available options:

###Export

Will do a export from the kps database.

Example:

```
echo $application->get('keepass')
                 ->getKpScript()
                 ->get('export')
                 ->setOutput('/dev/stdout')
                 ->setFormat(Export::FORMAT_KEEPASS_2_XML)
                 ->run();
```
###GetEntryString

Retrieves the value of an entry string field.

Example:
```
echo $application->get('keepass')
                 ->getKpScript()
                 ->get('get_entry_string')
                 ->setField('Password')
                 ->setRef('UUID','XXXXXXXXXXXXXXXXXXXXXXXX')
                 ->run();
```
Supported field names are e.g. Title, UserName, Password, URL, Notes, etc.

###ListEntries

Export entire entry list

Example
```
echo $application->get('keepass')
                 ->getKpScript()
                 ->get('list_entries')
                 ->run();
```

###ListGroups

Export entire group list

Example
```
echo $application->get('keepass')
                 ->getKpScript()
                 ->get('list_groups')
                 ->run();
```

###GenPw

Generate password bij keepass profile/standard

```
echo $container->get('keepass')
               ->getKpScript()
               ->get('GenPw')
               ->setCount(5)
               ->setProfile(GenPw::PROFILE_256_BIT)
               ->run();
```
 Methods setCount and setProfile are optional, on default it will generate 1 password and use profile PROFILE_RANDOM_STRING

 Available profiles:

     PROFILE_40_BIT         40-Bit Hex Key
     PROFILE_128_BIT        128-Bit Hex Key
     PROFILE_256_BIT        256-Bit Hex Key,
     PROFILE_RANDOM_MAC     Random MAC Address,
     PROFILE_RANDOM_STRING  Generates a random string






