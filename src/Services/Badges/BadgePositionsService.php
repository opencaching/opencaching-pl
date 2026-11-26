<?php

declare(strict_types=1);

namespace src\Services\Badges;

use src\Models\Badges\BadgePositionsRepository;
use src\Models\Badges\BadgesRepository;

/**
 * Business logic for badge positions list.
 */
class BadgePositionsService
{
    private readonly BadgePositionsRepository $repository;
    private readonly BadgesRepository $badgesRepository;

    public function __construct(
        ?BadgePositionsRepository $repository = null,
        ?BadgesRepository $badgesRepository = null
    ) {
        $this->repository = $repository ?? new BadgePositionsRepository();
        $this->badgesRepository = $badgesRepository ?? new BadgesRepository();
    }

    /**
     * Check whether a user exists.
     */
    public function userExists(int $userId): bool
    {
        return $this->repository->userExists($userId);
    }

    /**
     * Read a badge by id.
     *
     * @return array<string, mixed>|null
     */
    public function findBadge(int $badgeId): ?array
    {
        return $this->repository->findBadge($badgeId);
    }

    /**
     * Read badge images (badge picture + level pictures).
     *
     * @return array<int, string>
     */
    public function findBadgeImages(int $badgeId): array
    {
        $images = [];

        $badgePicture = $this->badgesRepository->findBadgePicture($badgeId);
        if ($badgePicture !== null) {
            $images[] = $badgePicture;
        }

        foreach ($this->badgesRepository->findBadgeLevelPictures($badgeId) as $picture) {
            $images[] = $picture;
        }

        $unique = [];
        foreach ($images as $image) {
            if (!in_array($image, $unique, true)) {
                $unique[] = $image;
            }
        }

        return $unique;
    }

    /**
     * Count positions of a badge.
     */
    public function countPositions(int $badgeId, string $belongingQuery, ?int $userId = null, string $foundFilter = 'all'): int
    {
        return $this->repository->countPositions($badgeId, $belongingQuery, $userId, $foundFilter);
    }

    /**
     * Read positions of a badge (paginated, sorted).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findPositions(
        int $badgeId,
        string $belongingQuery,
        int $limit,
        int $offset,
        string $orderBy,
        ?int $userId = null,
        string $foundFilter = 'all'
    ): array {
        return $this->repository->findPositions(
            $badgeId,
            $belongingQuery,
            $limit,
            $offset,
            $orderBy,
            $userId,
            $foundFilter
        );
    }
}