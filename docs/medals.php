Adding new type of medal
========================

I. Overview

Medal module is designed elastic (can allow unlimited medals diffrent unlimited types). 

works on two basic structures: 
- abstract 'medal types', representing by separate php classes
- phisics 'medal', created dynamicly by MedalController on basic of data from configuration table.

Module is designed to allow unlimited number of medals within each medal type. Number of Medal types 
is unlimited too.

MedalType1   => medal1
             => medal2
             => medal3
             (...)
             => medaln
             
MedalType2  => medal(n+1)
            => medal(n+2)
            (...)
            => medal(n+m)
            (...)
            
MedalTypeX  => medal(n+m+1)
            => medal(n+m+2)
            (...)
            => medal(n+m+3)            
             
It is possible to add new medals of existing medal types without programming skills. 
=> If you want add new medal Refer to chapter II

To add new medal type programig will be required. 

===================================================================================================
II. Steps to create new medal for existing types of medals.
Wystarczy dodać do tablicy konfiguracyjnej kolejny składnik. Moduł sobie po nich iteruje i jak
znajdzie nową konfigurację medalu to go stworzy. (Można dodać dowolną ilość medali dowolnego typu.)

===================================================================================================
III. Steps to create new medal type.
Each medal type is built by php class, whch overloads object \lib\Objects\Medals\Medal and implements
interface \lib\Objects\Medals\MedalInterface.
Class must contain method, which checks conditions for medal specified type.

Let's say We want add new type of medal, awarded for Geocaching Activity in Mountains
Let's name it: Mountain Geocacher

1. Add new type of medal
open lib\Controllers\MedalsController.php and declare new medal type: add next constant:

example:
<?php
class MedalsController
{
    const MEDAL_TYPE_REGION = 1;
    (...)
    const MEDAL_TYPE_MAXALTITUDE = 4;
    const MEDAL_TYPE_HIGHLAND = 5;
?>

in the same class, add your funbuildMedalObject

constant in Class MedalsContainer

2. Add new class file in lib/Object/Medals => MedalHighlandCaches.php
create class called HighlandCaches. Class have to extends class \lib\Objects\Medals\Medal and implements interface \lib\Objects\Medals\MedalInterface.

example:
<?php
namespace lib\Objects\Medals;

class MedalHighlandCaches extends \lib\Objects\Medals\Medal implements \lib\Objects\Medals\MedalInterface
{
    public function checkConditionsForUser(\lib\Objects\User\User $user)
    {

    }
}
?>

Back to the MedalsController, and in method buildMedalObject() add to dispatch your class for your medal type:

add:
<?php
    case self::MEDAL_TYPE_HIGHLAND:
        return new \lib\Objects\Medals\MedalHighlandCaches($medalDetails);
?>

4 Add new medal

open Go to \lib\Objects\Medals\MedalsContainer.php
at the bottom of class add new constant 

example:
<?php
class MedalsContainer
{
    const REGION_MALOPOLSKA = 1;
    const REGION_KRAKOW = 2;
    (...)
    const MAXALTITUDE_2450 = 6;
	const HIGHLAND_9000 = 7;       /* add this */
}
?>
note: constants MUST be unique, use next available integer instead 7


We have created New Medal type. Now, we have to add new medal.

4. Add new medal in medal configuration file

Open lib\Controllers\config\medals.config.php, and at the end add new element of configuration tabale, using your new medal type constant from MedalContainer class as main table key.

example 
<?php
return array(
    MedalsContainer::REGION_MALOPOLSKA => array( ... ),
    MedalsContainer::REGION_KRAKOW => array( ... ),
    (...)
    MedalsContainer::HIGHLAND_9000 => array(), /* <==== add this */
?>
Now buld configuration table body for your new medal. Tabe have to contain keys:
    'name' => YourMedalName
    'child' => type of medal, use constant for specified type from MedalsController, f.example MedalsController::MEDAL_TYPE_HIGHLAND
    'dateIntroduced' => '2005-01-01 00:01:00', 
    'conditions' => array( conditions user have to meet to be awarded. body of this array depends on you )

example for our mountain medal:
<?php
     MedalsContainer::HIGHLAND_9000 => array(
        'name' => 'HighlandGeocacher',
        'child' => MedalsController::MEDAL_TYPE_HIGHLAND,
        'dateIntroduced' => '2005-01-01 00:01:00',
        'conditions' => array(
            'ocNodeId' => array( /* medal can be awarded for users of following nodes. nodes not listed will be ignored */
                OcConfig::OCNODE_POLAND,
                OcConfig::OCNODE_ROMANIA
            ),
            'cacheType' => array( /* geocaches types to be counted for awarding medal. types not listed will be ignored */
                GeoCache::TYPE_TRADITIONAL,
                GeoCache::TYPE_MULTICACHE,
                GeoCache::TYPE_QUIZ,
            ),
            'altitudeToAward' => 500, /* minimum altitude of caches to be counted for awarding medal (caches less than 500 m altitude will be ignored) */
            'cacheCountToAward' => array(
                 1 => array(                   /* <= level id */
                    'levelName' => 'Paper',
                    'cacheCount' => array(      /* to get this medal level user have to: */
                        'found' => 1,           /*  found 1 cache */
                        'placed' => 0,			/*  place 0 caches */
                    ),
                ),
                2 => array(
                    'levelName' => 'Wooden',
                    'cacheCount' => array( 		/* to get this medal level user have to: */
                        'found' => 5,			/*  found 5 cache */
                        'placed' => 1,			/*  place 1 cache */
                    ),
                ),
                3 => array(
                    'levelName' => 'Iron',
                    'cacheCount' => array(
                        'found' => 10,
                        'placed' => 2,
                    ),
                ),

                (and so on, you can add as many levels as you want.)

                ),
            ),
             (if you create new medal type, you can add as many your own conditions as you wisch, remeber to program it in your new medal type class HighlandCaches)
        ),
    ),
?>

5. add medal graphics.
create direcotry in tpl/stdstyle/medals/ call it [constantOfYourMedal]

[constantOfYourMedal] is integer, the same as constant you created in MedalsContainer. In this example: HIGHLAND_9000 = 7, so directory name is 7
place image files for your medal levels named 1.png, 2.png ... n.png. Path to display images will be created automaticly.

6. Program method checking if your medal conditions were met for user.

Now, we have builded (abstract) new medal type MedalsController::MEDAL_TYPE_HIGHLAND,
and declared (psihics) medal MedalsContainer::HIGHLAND_9000.
All we need, to write body of method checkConditionsForUser() in your HighlandCaches class.

Medal Controller daily run function checkAllUsersMedals(). This function perform medal check on each user. 
Iterate through all medals class, calling method checkConditionsForUser(). All you need is edit your checkConditionsForUser()

6.1 to make your develop works easy, create test script helping you to develop, place it in main oc directory.

<?php

require_once 'lib/kint/Kint.class.php';
require_once 'lib/common.inc.php';
error_reporting(-1);
$medals = new \lib\Controllers\MedalsController();
$medals->checkAllUsersMedals();
?>

6.2 first login as any of end user in your develop opencaching.
then open your class HighlandCaches, add some debug.
<?php
public function checkConditionsForUser(\lib\Objects\User\User $user)
{
	dd($user);
}
?>

and run test script. It should output first user object to be check. 
Now is your creative job to do. You need witchdraw from database all data you need to be checked if user met all conditions for your medal

finally your methode may looks like:
<?php
public function checkConditionsForUser(\lib\Objects\User\User $user)
{
	d($user);
    d($this->conditions); /* displays conditions for current medal from config file */

	$userMetConditionsForMedal = true; /* [true / false] place here code
                                       * checking if all of $this->conditions were met */
	$level = 5; /* place here your code checking wchich level for $this->conditions['cacheCountToAward']
                 * were met */

	if($userMetConditionsForMedal){ /* set medal were awarded */
		$this->setMedalPrizedTimeAndAcheivedLevel($level);
	}

	$this->storeMedalStatus($user); /* finally this will automaticly store checking medal result in database */
	dd($this->prizedTime, $this->level); /* display if medal were prized, stop executing*/
}
?>

after your script is finished, remeber to remove all dumps (d(), dd()).
That's it!!