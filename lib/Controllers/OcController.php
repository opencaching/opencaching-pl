<?php

namespace lib\Controllers;

require_once __DIR__ . '/../ClassPathDictionary.php';
require_once __DIR__ . '/../kint/Kint.class.php'; /* magnificant debug tool */

/**
 * Description of OcController
 *
 * @author Łza
 */
class OcController
{
    private $request;

    public function run($request)
    {
        d('idzie przez OcController');
        d($request, $_POST);
        $this->request = $request;

        switch ($request['action']) {
            case 'userMedals':
                return $this->userMedals();
            default:
                break;
        }
    }

    private function userMedals()
    {
        $user = new \lib\Objects\User\User($this->request['userId']);
        d($user);
         /* @var $medal \lib\Objects\Medals\Medal */
        foreach ($user->getMedals() as $medal) {
            print '<div><img src="'.$medal->getImage().'" width=150 /><br>'.$medal->getName().' poziom '.$medal->getLevel().'</div>';
        }
    }
}
