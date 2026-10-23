<?php

namespace src\Controllers\SysAdmin;

use RuntimeException;
use src\Controllers\Core\ViewBaseController;
use src\Enums\RoleManagerEnum;
use src\Enums\RoleManagerSortEnum;
use src\Models\ChunkModels\PaginationModel;
use src\Models\User\RoleManagerRepository;
use src\Services\RoleManagerService;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\Uri;

/**
 * Sysadmin-only role management panel.
 *
 * The constructor rejects users without the sysAdmin role with HTTP 403.
 */
class RoleManagerController extends ViewBaseController
{
    private const HTTP_METHOD_NOT_ALLOWED = 405;

    private const RESULT_MESSAGES = [
        'roles_updated' => 'role_manager_roles_updated',
        'roles_removed' => 'role_manager_roles_removed',
        'user_not_found' => 'role_manager_user_not_found',
        'invalid_role' => 'role_manager_invalid_role',
        'cannot_remove_own_sysadmin_role' => 'role_manager_cannot_remove_own_sysadmin_role',
        'last_sysadmin' => 'role_manager_last_sysadmin',
        'invalid_user' => 'role_manager_invalid_user',
        'invalid_action' => 'role_manager_invalid_action',
    ];

    private RoleManagerRepository $repository;

    private RoleManagerService $roleService;

    public function __construct()
    {
        parent::__construct();

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasSysAdminRole()) {
            $this->displayCommonErrorPageAndExit(
                'Not authorized for this operation',
                HttpCode::STATUS_FORBIDDEN
            );
        }

        $this->repository = new RoleManagerRepository();
        $this->roleService = new RoleManagerService($this->repository);
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'setRoles', 'removeAllRoles'], true);
    }

    /**
     * Route: SysAdmin.RoleManager
     *
     * List of users with roles, filters, search and pagination.
     */
    public function index()
    {
        $search = $this->getSearchParameter();
        $role = $this->getRoleParameter();
        $sort = $this->getSortParameter();
        $page = $this->getPageParameter();

        // The list contains only users with roles. To find users without any role,
        // the sysadmin has to select the special "no role" filter, which searches
        // the whole user table but restricts the result to users with an empty role.
        $onlyWithRoles = $role !== RoleManagerRepository::ROLE_FILTER_NONE;

        $users = $this->roleService->getUsers(
            $search,
            $role,
            $page,
            $sort,
            $onlyWithRoles
        );

        $paginationModel = new PaginationModel(RoleManagerEnum::DEFAULT_LIMIT);
        $paginationModel->setRecordsCount($users['total']);

        $this->view->setVar('users', $users['items']);
        $this->view->setVar('totalCount', $users['total']);
        $this->view->setVar('limit', $users['limit']);
        $this->view->setVar('filters', [
            'search' => $search,
            'role' => $role,
            'sort' => $sort,
            'onlyWithRoles' => $onlyWithRoles,
        ]);
        $this->view->setVar('roles', $this->roleService->getRoles());
        $this->view->setVar('roleOptions', $this->roleService->getRoleOptions());
        $this->view->setVar('sortOptions', $this->roleService->getSortOptions());
        $this->view->setVar('currentUserId', $this->loggedUser->getUserId());
        $this->view->setVar('paginationModel', $paginationModel);

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/js/public.js'),
            false,
            true
        );

        $this->view->setTemplate('sysAdmin/roleManager/index');
        $this->view->buildView();
    }

    /**
     * Route: SysAdmin.RoleManager/setRoles/{userId}
     *
     * AJAX endpoint which replaces the user's roles with the selected set.
     */
    public function setRoles(int $userId): void
    {
        if (! $this->isPostRequest()) {
            $this->sendAjaxErrorResponse(
                'invalid_action',
                'POST required',
                self::HTTP_METHOD_NOT_ALLOWED
            );
        }

        if ($userId <= 0) {
            $this->sendAjaxErrorResponse(
                'invalid_user',
                'Invalid user',
                HttpCode::STATUS_BAD_REQUEST
            );
        }

        $roleValues = $this->getRoleValuesFromPost();

        try {
            $result = $this->roleService->setRoles($userId, $roleValues);
        } catch (RuntimeException $exception) {
            $this->sendAjaxErrorResponse(
                'invalid_action',
                $exception->getMessage(),
                $this->getExceptionStatus($exception)
            );

            return;
        }

        if (! $result['success']) {
            $this->sendAjaxErrorResponse(
                $result['code'],
                $result['message'],
                $this->getErrorStatus($result['code'])
            );

            return;
        }

        $this->sendAjaxSuccessResponse($result['code'], [
            'user' => $result['user'],
            'roleValues' => $result['user']['roleValues'] ?? [],
        ]);
    }

    /**
     * Route: SysAdmin.RoleManager/removeAllRoles/{userId}
     *
     * AJAX endpoint which removes all roles from a user.
     */
    public function removeAllRoles(int $userId): void
    {
        if (! $this->isPostRequest()) {
            $this->sendAjaxErrorResponse(
                'invalid_action',
                'POST required',
                self::HTTP_METHOD_NOT_ALLOWED
            );
        }

        if ($userId <= 0) {
            $this->sendAjaxErrorResponse(
                'invalid_user',
                'Invalid user',
                HttpCode::STATUS_BAD_REQUEST
            );
        }

        try {
            $result = $this->roleService->removeAllRoles($userId);
        } catch (RuntimeException $exception) {
            $this->sendAjaxErrorResponse(
                'invalid_action',
                $exception->getMessage(),
                $this->getExceptionStatus($exception)
            );

            return;
        }

        if (! $result['success']) {
            $this->sendAjaxErrorResponse(
                $result['code'],
                $result['message'],
                $this->getErrorStatus($result['code'])
            );

            return;
        }

        $this->sendAjaxSuccessResponse($result['code'], [
            'user' => $result['user'],
            'roleValues' => $result['user']['roleValues'] ?? [],
        ]);
    }

    private function isPostRequest(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    /**
     * @return array<int, string>
     */
    private function getRoleValuesFromPost(): array
    {
        $roleValues = $_POST['roles'] ?? $_POST['role'] ?? [];

        if (is_string($roleValues)) {
            return [$roleValues];
        }

        if (! is_array($roleValues)) {
            return [];
        }

        return array_values(array_filter($roleValues, 'is_string'));
    }

    private function getExceptionStatus(RuntimeException $exception): int
    {
        $status = (int) $exception->getCode();

        return $status >= 400 && $status < 600
            ? $status
            : HttpCode::STATUS_INTERNAL_ERROR;
    }

    private function getErrorStatus(string $code): int
    {
        return match ($code) {
            'user_not_found' => HttpCode::STATUS_NOT_FOUND,
            'cannot_remove_own_sysadmin_role', 'last_sysadmin' => HttpCode::STATUS_CONFLICT,
            'invalid_user', 'invalid_role', 'invalid_action' => HttpCode::STATUS_BAD_REQUEST,
            default => HttpCode::STATUS_INTERNAL_ERROR,
        };
    }

    private function getResultMessage(string $code, ?string $fallback = null): string
    {
        if (isset(self::RESULT_MESSAGES[$code])) {
            return tr(self::RESULT_MESSAGES[$code]);
        }

        return $fallback ?? tr('role_manager_invalid_action');
    }

    private function sendAjaxErrorResponse(string $code, string $fallbackMessage, int $statusCode): void
    {
        $this->sendAjaxJsonResponse([
            'status' => 'ERROR',
            'code' => $code,
            'message' => $this->getResultMessage($code, $fallbackMessage),
        ], $statusCode);
    }

    private function sendAjaxSuccessResponse(string $code, array $additionalData = []): void
    {
        $this->sendAjaxJsonResponse(array_merge($additionalData, [
            'status' => 'OK',
            'code' => $code,
            'message' => $this->getResultMessage($code),
        ]), HttpCode::STATUS_OK);
    }

    private function sendAjaxJsonResponse(array $response, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getSearchParameter(): ?string
    {
        $search = $this->sanitizeSearch($_GET['search'] ?? null);

        return $search === '' ? null : $search;
    }

    private function sanitizeSearch(mixed $value): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        return trim(strip_tags((string) $value));
    }

    private function getRoleParameter(): ?string
    {
        if (! isset($_GET['role']) || ! is_string($_GET['role'])) {
            return null;
        }

        if ($_GET['role'] === RoleManagerRepository::ROLE_FILTER_NONE) {
            return RoleManagerRepository::ROLE_FILTER_NONE;
        }

        return RoleManagerEnum::tryFrom($_GET['role'])?->value;
    }

    private function getSortParameter(): string
    {
        $sort = $_GET['sort'] ?? null;

        return RoleManagerSortEnum::fromValue(is_string($sort) ? $sort : null)->value;
    }

    private function getPageParameter(): int
    {
        $page = $_GET['_page'] ?? 1;

        if (! is_numeric($page) || $page < 1) {
            return 1;
        }

        return (int) $page;
    }
}
