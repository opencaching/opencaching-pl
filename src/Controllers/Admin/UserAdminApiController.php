<?php
namespace src\Controllers\Admin;

use src\Controllers\Core\ApiBaseController;
use src\Models\User\User;

class UserAdminApiController extends ApiBaseController
{
   function __construct()
   {
       parent::__construct();
       if (!$this->isUserLogged() || !$this->loggedUser->hasOcTeamRole()) {
           // this controller is accessible only for OCTeam
           $this->ajaxErrorResponse("Not authorized for this operation", self::HTTP_UNAUTHORIZED);
       }
   }

    /**
     * Ajax request to remove user account
     *
     * @param int $userId
     */
    public function removeUserAccount(int $userId = null): void
    {
        if (!$userId) {
            $this->ajaxErrorResponse("Wrong request", self::HTTP_STATUS_BAD_REQUEST);
        }

        /** @var User $accountToRemove */
        $accountToRemove = User::fromUserIdFactory($userId);
        if (!$accountToRemove) {
            $this->ajaxErrorResponse("No such user", self::HTTP_STATUS_NOT_FOUND);
        }

        // check if account is already locked
        if ($accountToRemove->isAlreadyRemoved()) {
            $this->ajaxErrorResponse("Already removed", self::HTTP_STATUS_BAD_REQUEST);
        }

        $accountToRemove->removeAccount($this->loggedUser);
        $this->ajaxSuccessResponse("Account and all its data removed");
    }
}
