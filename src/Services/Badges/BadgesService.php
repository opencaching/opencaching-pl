<?php

declare(strict_types=1);

namespace src\Services\Badges;

use src\Models\Badges\BadgesRepository;

/**
 * Business logic for badges.
 */
class BadgesService
{
    private readonly BadgesRepository $repository;

    public function __construct(?BadgesRepository $repository = null)
    {
        $this->repository = $repository ?? new BadgesRepository();
    }

    /**
     * Read distinct owner ids of a badge.
     *
     * @return array<int, int>
     */
    public function findDistinctOwnerIds(int $badgeId): array
    {
        return $this->repository->findDistinctOwnerIds($badgeId);
    }

    /**
     * Check whether the given user is an owner of the badge.
     */
    public function isOwner(int $badgeId, int $userId): bool
    {
        return $this->repository->isOwner($badgeId, $userId);
    }

    /**
     * Read badge statistics: all positions, active count, gained count.
     *
     * @return array{positions: array<int,int>, activeCount: int, gained: array<int,array<string,mixed>>, gainedCount: int}
     */
    public function getBadgeStats(int $badgeId, int $userId): array
    {
        $positions = $this->repository->findBadgePositions($badgeId);
        $gained = $this->repository->findGainedPositions($badgeId, $userId);

        return [
            'positions' => $positions,
            'positionsCount' => count($positions),
            'activeCount' => $this->repository->countActiveBadgePositions($badgeId),
            'gained' => $gained,
            'gainedCount' => count($gained),
        ];
    }

    /**
     * Read a single badge by its id.
     *
     * @return array<string, mixed>|null
     */
    public function findBadgeById(int $badgeId): ?array
    {
        return $this->repository->findBadgeById($badgeId);
    }

    /**
     * Read all categories (in their configured order) together with their
     * active badges (ordered according to the category's sort direction).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllActiveBadgesGroupedByCategory(): array
    {
        $categories = $this->repository->findAllCategories();
        $result = [];

        foreach ($categories as $index => $category) {
            $badges = $this->repository->findBadgesByCategory(
                (int) $category['id'],
                (bool) $category['sort_desc']
            );

            $result[$index] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'sort_desc' => (bool) $category['sort_desc'],
                'badges' => $badges,
            ];

            // Remove categories that have no active badges.
            if ($badges === []) {
                unset($result[$index]);
            }
        }

        return array_values($result);
    }
}
