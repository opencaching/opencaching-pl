<?php

declare(strict_types=1);

namespace src\Controllers\User;

use src\Controllers\Core\ViewBaseController;
use src\Models\User\User;
use src\Services\UserServiceMatrix;
use src\Utils\Uri\HttpCode;

/**
 * Displays cache matrices for a user.
 */
class MatrixController extends ViewBaseController
{
    private readonly UserServiceMatrix $matrixService;

    public function __construct()
    {
        parent::__construct();

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole() && ! $this->loggedUser->hasAdvUserRole()) {
            $this->displayCommonErrorPageAndExit(
                'Not authorized for this operation',
                HttpCode::STATUS_FORBIDDEN
            );
        }

        $this->matrixService = new UserServiceMatrix();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return $actionName === 'index';
    }

    public function index($userId = null): void
    {
        $userId = $userId === null
            ? $this->loggedUser->getUserId()
            : (int) $userId;

        $matrixUser = $userId > 0 ? User::fromUserIdFactory($userId, 'user_id, username') : null;

        if ($matrixUser === null) {
            $this->displayCommonErrorPageAndExit('User not found', HttpCode::STATUS_NOT_FOUND);
        }

        $this->view->setVar('matrixUserId', $matrixUser->getUserId());
        $this->view->setVar('matrixUsername', $matrixUser->getUserName());
        $this->view->setVar(
            'matrix',
            $this->matrixService->getDifficultyTerrainMatrix($userId)
        );
        $this->view->setTemplate('user/matrix');
        $this->view->buildView();
    }
}
