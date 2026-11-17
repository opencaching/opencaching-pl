<?php

declare(strict_types=1);

namespace src\Services\Badges;

use src\Models\Badges\BadgesRepository;
use src\Utils\I18n\I18n;
use src\Utils\Text\SmilesInText;
use src\Utils\Text\UserInputFilter;

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
     * Read and group badge owners by owner type.
     *
     * Returns an array of groups, each with:
     *  - typeId: int
     *  - translationKey: string
     *  - owners: array of ['id' => int, 'username' => string]
     *
     * Groups are ordered by type id ascending; owners within a group by position.
     * Returns [] when the badge has no owners.
     *
     * @return array<int, array{typeId:int, translationKey:string, owners:array<int,array{id:int,username:string}>}>
     */
    public function getBadgeOwners(int $badgeId): array
    {
        $rows = $this->repository->findBadgeOwners($badgeId);

        $groups = [];
        $groupIndex = [];

        foreach ($rows as $row) {
            $typeId = (int) $row['id_owner_type'];

            if (!isset($groupIndex[$typeId])) {
                $groupIndex[$typeId] = count($groups);
                $groups[] = [
                    'typeId' => $typeId,
                    'translationKey' => (string) ($row['translation_key'] ?? ''),
                    'owners' => [],
                ];
            }

            $groups[$groupIndex[$typeId]]['owners'][] = [
                'id' => (int) $row['id_owner'],
                'username' => (string) ($row['username'] ?? ''),
            ];
        }

        return $groups;
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
     * Determine the description language to display.
     *
     * Order: requested lang (if available) -> current user lang -> first available.
     *
     * @return string|null
     */
    public function getDescriptionLanguage(int $badgeId, ?string $requestedLang = null): ?string
    {
        $available = $this->repository->findAvailableDescriptionLanguages($badgeId);

        if ($available === []) {
            return null;
        }

        if ($requestedLang !== null && in_array($requestedLang, $available, true)) {
            return $requestedLang;
        }

        $currentLang = strtoupper(I18n::getCurrentLang());
        if (in_array($currentLang, $available, true)) {
            return $currentLang;
        }

        return $available[0];
    }

    /**
     * Read and prepare the badge description for display.
     *
     * Returns purified HTML with smiles processed. Returns null if no description.
     *
     * @return string|null
     */
    public function getDescription(int $badgeId, ?string $requestedLang = null): ?string
    {
        $lang = $this->getDescriptionLanguage($badgeId, $requestedLang);

        if ($lang === null) {
            return null;
        }

        $row = $this->repository->findDescription($badgeId, $lang);

        if ($row === null) {
            return null;
        }

        $desc = (string) ($row['description'] ?? '');

        // Purify if needed (badge descriptions are stored as safe HTML).
        $desc = UserInputFilter::purifyHtmlString($desc);

        // Process smiles.
        $desc = SmilesInText::process($desc);

        return $desc;
    }

    /**
     * Read all images for a badge.
     *
     * Order: badge picture first, then level pictures (ascending level).
     * Duplicates are removed. Ordering/dedup is business logic, hence here.
     *
     * @return array<int, string>
     */
    public function findLevels(int $badgeId): array
    {
        return $this->repository->findLevels($badgeId);
    }

    /**
     * Read recommended caches for a badge.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findRecommendedCaches(int $badgeId): array
    {
        return $this->repository->findRecommendedCaches($badgeId);
    }

    /**
     * Read caches of the week for a badge.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTitledCaches(int $badgeId): array
    {
        return $this->repository->findTitledCaches($badgeId);
    }

    /**
     * Read all images for a badge.
     *
     * Order: badge picture first, then level pictures (ascending level).
     * Duplicates are removed.
     *
     * @return array<int, string>
     */
    public function findBadgeImages(int $badgeId): array
    {
        $images = [];

        $badgePicture = $this->repository->findBadgePicture($badgeId);
        if ($badgePicture !== null) {
            $images[] = $badgePicture;
        }

        foreach ($this->repository->findBadgeLevelPictures($badgeId) as $picture) {
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
                (int) $category['id']
            );

            $result[$index] = [
                'id' => $category['id'],
                'name' => $category['name'],
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
