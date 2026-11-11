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
