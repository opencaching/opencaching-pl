<?php

declare(strict_types=1);

namespace src\Controllers\User;

use src\Controllers\Core\ViewBaseController;
use src\Models\User\User;
use src\Services\UserMatrixService;
use src\Utils\Uri\HttpCode;

/**
 * Displays cache matrices for a user.
 */
class MatrixController extends ViewBaseController
{
    private readonly UserMatrixService $matrixService;

    /**
     * Create the controller and restrict access to OC Team or advanced users.
     */
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

        $this->matrixService = new UserMatrixService();
    }

    /**
     * Allow only the index action to be called from the router.
     */
    public function isCallableFromRouter(string $actionName): bool
    {
        return $actionName === 'index';
    }

    /**
     * Display the cache matrix for the requested or logged in user.
     *
     * @param int|string|null $userId Optional user ID from the route.
     */
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
        $this->view->setVar(
            'monthDayMatrix',
            $this->matrixService->getMonthDayMatrix($userId)
        );
        $this->view->setVar(
            'yearMonthMatrix',
            $this->matrixService->getYearMonthMatrix($userId)
        );
        $this->view->setTemplate('user/matrix');
        $this->view->buildView();
    }
}
