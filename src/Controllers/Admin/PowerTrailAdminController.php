<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ViewBaseController;
use src\Enums\PowerTrailAdminEnum;
use src\Enums\PowerTrailStatusesEnum;
use src\Enums\PowerTrailTypesEnum;
use src\Models\ChunkModels\PaginationModel;
use src\Models\CacheSet\CacheSet;
use src\Models\GeoCache\GeoCacheStatusRepository;
use src\Models\PowerTrail\PowerTrailAdminRepository;
use src\Utils\I18n\I18n;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

class PowerTrailAdminController extends ViewBaseController
{
    private PowerTrailAdminRepository $repository;

    public function __construct()
    {
        parent::__construct();

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->displayCommonErrorPageAndExit(
                'Not authorized for this operation',
                HttpCode::STATUS_FORBIDDEN
            );
        }

        $this->repository = new PowerTrailAdminRepository();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'editPowerTrail', 'refreshPowerTrail', 'removeCache', 'changeStatus'], true);
    }

    public function index()
    {
        $id = $this->getIntegerQueryParameter('id', 0);
        $id = $id > 0 ? $id : null;
        $name = isset($_GET['name']) && is_string($_GET['name'])
            ? trim($_GET['name'])
            : null;
        $name = $name === '' ? null : $name;
        $sort = $this->getIntegerQueryParameter('sort', PowerTrailAdminEnum::SORT_ID_DESC);

        if (! array_key_exists($sort, PowerTrailAdminEnum::sortOptions())) {
            $sort = PowerTrailAdminEnum::SORT_ID_DESC;
        }

        $totalCount = $this->repository->countAll($id, $name);
        $paginationModel = new PaginationModel(PowerTrailAdminEnum::LIMIT);
        $paginationModel->setRecordsCount($totalCount);
        [$limit, $offset] = $paginationModel->getQueryLimitAndOffset();

        $this->view->setVar(
            'powerTrails',
            $this->repository->findAll($limit, $offset, $sort, $id, $name)
        );
        $this->view->setVar('totalCount', $totalCount);
        $this->view->setVar('types', PowerTrailTypesEnum::types());
        $this->view->setVar('statuses', PowerTrailStatusesEnum::statuses());
        $this->view->setVar('filters', [
            'id' => $id,
            'name' => $name,
            'sort' => $sort,
        ]);
        $this->view->setVar('sortOptions', PowerTrailAdminEnum::sortOptions());
        $this->view->setVar('limit', PowerTrailAdminEnum::LIMIT);
        $this->view->setVar('paginationModel', $paginationModel);

        $this->view->setTemplate('admin/powerTrailAdmin/index');
        $this->view->buildView();
    }

    public function editPowerTrail(int $powerTrailId)
    {
        $powerTrail = $this->repository->findById($powerTrailId);
        if ($powerTrail === null) {
            $this->displayCommonErrorPageAndExit(
                'GeoPath not found',
                HttpCode::STATUS_NOT_FOUND
            );
        }

        $this->view->setVar('powerTrail', $powerTrail);
        $this->view->setVar('caches', $this->repository->findCaches($powerTrailId));
        $cacheStatuses = [];
        foreach ((new GeoCacheStatusRepository())->findAll(I18n::getCurrentLang()) as $status) {
            $cacheStatuses[(int) $status['id']] = $status['status_name'];
        }
        $this->view->setVar('cacheStatuses', $cacheStatuses);
        $this->view->setVar('types', PowerTrailTypesEnum::types());
        $this->view->setVar('statuses', PowerTrailStatusesEnum::statuses());
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/js/public.js'),
            false,
            true
        );
        $this->view->setTemplate('admin/powerTrailAdmin/edit');
        $this->view->buildView();
    }

    /**
     * Route: Admin.PowerTrailAdmin/refreshPowerTrail/{powerTrailId}
     * Recalculates center point, points and caches count of the GeoPath.
     */
    public function refreshPowerTrail(int $powerTrailId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->displayCommonErrorPageAndExit(
                'POST required',
                405
            );
        }

        $geoPath = CacheSet::fromCacheSetIdFactory($powerTrailId);
        if (! $geoPath) {
            $this->displayCommonErrorPageAndExit(
                'GeoPath not found',
                HttpCode::STATUS_NOT_FOUND
            );
        }

        $geoPath->recalculateCenterPoint();
        $geoPath->updatePoints();
        $geoPath->updateCachesCount();

        SimpleRouter::redirect(
            SimpleRouter::getLink(self::class, 'editPowerTrail', $powerTrailId)
        );
    }

    /**
     * Route: Admin.PowerTrailAdmin/changeStatus/{powerTrailId}
     */
    public function changeStatus(int $powerTrailId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->sendAjaxErrorResponse('POST required', 405);
        }

        $status = filter_var($_POST['status'] ?? null, FILTER_VALIDATE_INT);
        $allowedStatuses = [
            PowerTrailStatusesEnum::STATUS_OPEN,
            PowerTrailStatusesEnum::STATUS_INSERVICE,
            PowerTrailStatusesEnum::STATUS_CLOSED,
        ];
        if ($powerTrailId <= 0 || $status === false || ! in_array($status, $allowedStatuses, true)) {
            $this->sendAjaxErrorResponse('Invalid GeoPath ID or status', HttpCode::STATUS_BAD_REQUEST);
        }

        if ($this->repository->findById($powerTrailId) === null) {
            $this->sendAjaxErrorResponse('GeoPath not found', HttpCode::STATUS_NOT_FOUND);
        }

        $this->repository->updateStatus($powerTrailId, $status);

        $this->sendAjaxSuccessResponse('GeoPath status changed.', [
            'status_id' => $status,
        ]);
    }

    /**
     * Route: Admin.PowerTrailAdmin/removeCache/{powerTrailId}
     * This endpoint is intentionally a stub until cache removal is implemented.
     */
    public function removeCache(int $powerTrailId): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->sendAjaxErrorResponse('POST required', 405);
        }

        $cacheId = filter_var($_POST['cacheId'] ?? null, FILTER_VALIDATE_INT);
        if ($powerTrailId <= 0 || $cacheId === false || $cacheId <= 0) {
            $this->sendAjaxErrorResponse('Invalid GeoPath or cache ID', HttpCode::STATUS_BAD_REQUEST);
        }

        if ($this->repository->findById($powerTrailId) === null) {
            $this->sendAjaxErrorResponse('GeoPath not found', HttpCode::STATUS_NOT_FOUND);
        }

        if (! $this->repository->removeCacheFromPowerTrail($powerTrailId, $cacheId)) {
            $this->sendAjaxErrorResponse('Cache is not assigned to this GeoPath', HttpCode::STATUS_CONFLICT);
        }

        $this->sendAjaxSuccessResponse(
            'Cache removed from GeoPath.',
            ['localizedMessage' => tr('gp_cacheRemovedFromGeopath')]
        );
    }

    private function sendAjaxErrorResponse(string $message, int $statusCode): void
    {
        $this->sendAjaxJsonResponse([
            'status' => 'ERROR',
            'message' => $message,
        ], $statusCode);
    }

    private function sendAjaxSuccessResponse(string $message, array $additionalData = []): void
    {
        $this->sendAjaxJsonResponse(array_merge($additionalData, [
            'status' => 'OK',
            'message' => $message,
        ]), HttpCode::STATUS_OK);
    }

    private function sendAjaxJsonResponse(array $response, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getIntegerQueryParameter(string $name, int $default): int
    {
        if (! isset($_GET[$name]) || ! is_scalar($_GET[$name])) {
            return $default;
        }

        $value = filter_var((string) $_GET[$name], FILTER_VALIDATE_INT);

        return $value === false ? $default : max(0, $value);
    }
}
