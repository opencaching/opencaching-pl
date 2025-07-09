<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ApiBaseController;
use src\Models\User\User;
use src\Utils\Uri\HttpCode;

class UserAdminApiController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();

        if (! $this->isUserLogged() || ! $this->loggedUser->hasOcTeamRole()) {
            // this controller is accessible only for OCTeam
            $this->ajaxErrorResponse(
                'Not authorized for this operation',
                HttpCode::STATUS_UNAUTHORIZED
            );
        }
    }

    /**
     * Ajax request to remove user account
     */
    public function removeUserAccount(int $userId = null): void
    {
        if (! $userId) {
            $this->ajaxErrorResponse(
                tr('admin_user_rmResult_wrongRequest'),
                HttpCode::STATUS_BAD_REQUEST
            );
        }

        /** @var User $accountToRemove */
        $accountToRemove = User::fromUserIdFactory($userId);

        if (! $accountToRemove) {
            $this->ajaxErrorResponse(
                tr('admin_user_rmResult_noUser'),
                HttpCode::STATUS_NOT_FOUND
            );
        }

        // check if account is already locked
        if ($accountToRemove->isAlreadyRemoved()) {
            $this->ajaxErrorResponse(
                tr('admin_user_rmResult_alreadyRemoved'),
                HttpCode::STATUS_BAD_REQUEST
            );
        }

        $accountToRemove->removeAccount($this->loggedUser);
        $this->ajaxSuccessResponse(tr('admin_user_rmResult_success'));
    }
}
