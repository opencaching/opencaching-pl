<?php

namespace src\Services\Badges;

use RuntimeException;
use src\Enums\BadgeSortEnum;
use src\Models\Badges\BadgesRepository;

/**
 * Service for badge admin operations.
 */
class BadgesAdminService
{
    private BadgesRepository $repository;

    /**
     * __construct.
     * @param BadgesRepository|null $repository
     */
    public function __construct(?BadgesRepository $repository = null)
    {
        $this->repository = $repository ?? new BadgesRepository();
    }

    /**
     * getBadges.
     * @param string $search
     * @param BadgeSortEnum $sort
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getBadges(string $search, BadgeSortEnum $sort, int $page, int $perPage): array
    {
        $total = $this->repository->countBadgesAdmin($search);
        $items = $this->repository->findAllBadgesAdmin($search, $sort->sqlOrder(), $page, $perPage);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * getBadge.
     * @param int $id
     * @return array|null
     */
    public function getBadge(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        return $this->repository->findBadgeById($id);
    }

    /**
     * getBadgeLevels.
     * @param int $id
     * @return array
     */
    public function getBadgeLevels(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        return $this->repository->findLevelsRaw($id);
    }

    /**
     * getCategories.
     * @return array
     */
    public function getCategories(): array
    {
        return $this->repository->findAllCategories();
    }

    /**
     * updateBadgePicture.
     * @param int $badgeId
     * @param string $picture
     * @return void
     */
    public function updateBadgePicture(int $badgeId, string $picture): void
    {
        $this->repository->updateBadgePicture($badgeId, $picture);
    }

    /**
     * updateLevelPicture.
     * @param int $badgeId
     * @param int $level
     * @param string $picture
     * @return void
     */
    public function updateLevelPicture(int $badgeId, int $level, string $picture): void
    {
        $this->repository->updateLevelPicture($badgeId, $level, $picture);
    }

    /**
     * deleteBadgeImage.
     * @param int $badgeId
     * @return void
     */
    public function deleteBadgeImage(int $badgeId): void
    {
        $picture = $this->repository->deleteBadgeImage($badgeId);
        if ($picture === null) {
            return;
        }

        $dynBase = rtrim(\src\Models\OcConfig\OcConfig::getDynFilesPath(true), '/');
        $badgesDir = $dynBase . '/images/uploads/badges/main';

        $relative = ltrim($picture, '/');
        $fullPath = $dynBase . '/' . $relative;

        $realBase = realpath($badgesDir);
        $realFile = realpath($fullPath);

        if ($realBase !== false && $realFile !== false && strpos($realFile, $realBase . DIRECTORY_SEPARATOR) === 0) {
            if (is_file($realFile)) {
                @unlink($realFile);
            }
        }
    }

    /**
     * deleteLevelImage.
     * @param int $badgeId
     * @param int $level
     * @return void
     */
    public function deleteLevelImage(int $badgeId, int $level): void
    {
        $picture = $this->repository->deleteLevelImage($badgeId, $level);
        if ($picture === null) {
            return;
        }

        // Only delete files located in the dynamic files directory, under badges/.
        $dynBase = rtrim(\src\Models\OcConfig\OcConfig::getDynFilesPath(true), '/');
        $badgesDir = $dynBase . '/images/uploads/badges/levels';

        $relative = ltrim($picture, '/');
        // strip leading "images/" or similar? assume stored relative to dyn base or web root
        $fullPath = $dynBase . '/' . $relative;

        $realBase = realpath($badgesDir);
        $realFile = realpath($fullPath);

        if ($realBase !== false && $realFile !== false && strpos($realFile, $realBase . DIRECTORY_SEPARATOR) === 0) {
            if (is_file($realFile)) {
                @unlink($realFile);
            }
        }
    }

    /**
     * saveLevelNames.
     * @param int $badgeId
     * @param array $names
     * @return void
     */
    public function saveLevelNames(int $badgeId, array $names): void
    {
        foreach ($names as $level => $name) {
            $trimmed = trim((string) $name);
            if (mb_strlen($trimmed) > 30) {
                throw new RuntimeException('badge_level_name_too_long', 400);
            }
            $this->repository->saveLevelName($badgeId, (int) $level, $trimmed);
        }
    }

    /**
     * @param array{name:string,category_id:int,sequence:int,active:int} $data
     * @return int
     * @throws RuntimeException
     */
    public function saveBadge(int $id, array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $categoryId = (int) ($data['category_id'] ?? 0);
        $sequence = (int) ($data['sequence'] ?? 0);
        $active = array_key_exists('active', $data) ? (!empty($data['active']) ? 1 : 0) : null;

        if ($name === '' && $id > 0) {
            $existing = $this->repository->findBadgeById($id);
            $name = trim((string) ($existing['name'] ?? ''));
            if ($categoryId === 0) {
                $categoryId = (int) ($existing['category_id'] ?? 0);
            }
            if ($active === null) {
                $active = (int) ($existing['active'] ?? 0);
            }
        }

        if ($name === '') {
            throw new RuntimeException('form_error_field_is_required', 400);
        }
        if (mb_strlen($name) > 30) {
            throw new RuntimeException('form_error_field_too_long', 400);
        }

        if ($active === null) {
            $active = 0;
        }

        $this->repository->saveBadgeBasic($id, $name, $categoryId, $sequence, $active);

        return $id;
    }
}
