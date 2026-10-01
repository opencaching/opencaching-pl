<?php

namespace src\Controllers;

use src\Controllers\Core\ApiBaseController;
use src\Models\User\UserPreferences\NeighbourhoodPref;
use src\Models\User\UserPreferences\UserPreferences;

class MyNbhInteractiveApiController extends ApiBaseController
{
    /**
     * Saves changed order of MyNbh sections. Called via Ajax by MyNbh main page
     */
    public function changeOrder()
    {
        $this->checkUserLoggedAjax();
        $this->paramCheck('order');
        $order = [];
        parse_str($_POST['order'], $order);
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $counter = 1;

        foreach ($order['item'] as $itemOrder) {
            $preferences['items'][$itemOrder]['order'] = $counter;
            $counter++;
        }

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Saves changed size of MyNbh section. Called via Ajax by MyNbh main page
     */
    public function changeSize()
    {
        $this->checkUserLoggedAjax();
        $this->paramCheck('size');
        $this->paramCheck('item');
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['fullsize'] = filter_var(
            $_POST['size'],
            FILTER_VALIDATE_BOOLEAN
        );

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Saves display status of MyNbh section. Called via Ajax by MyNbh main page
     */
    public function changeDisplay()
    {
        $this->checkUserLoggedAjax();
        $this->paramCheck('hide');
        $this->paramCheck('item');
        $preferences = UserPreferences::getUserPrefsByKey(
            NeighbourhoodPref::KEY
        )->getValues();
        $itemNr = ltrim($_POST['item'], 'item_');
        $preferences['items'][$itemNr]['show'] = ! filter_var(
            $_POST['hide'],
            FILTER_VALIDATE_BOOLEAN
        );

        if (
            ! UserPreferences::savePreferencesJson(
                NeighbourhoodPref::KEY,
                json_encode($preferences)
            )
        ) {
            $this->ajaxErrorResponse('Error saving UserPreferences');
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Abstract function implementation,
     * (definition has to be compliant with parent class)
     */
    public function isCallableFromRouter(string $actionName): bool
    {
        return true;
    }

    /**
     * Check if $_POST[$paramName] is set. If not - generates 400 AJAX response
     *
     * @param string $paramName a name of parameter to check
     */
    private function paramCheck(string $paramName)
    {
        if (! isset($_POST[$paramName])) {
            $this->ajaxErrorResponse('No parameter: ' . $paramName, 400);

            exit();
        }
    }
}
