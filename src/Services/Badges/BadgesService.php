<?php

declare(strict_types=1);

namespace src\Services\Badges;

use src\Enums\RingThicknessEnum;
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
     * @param int $badgeId
     */
    public function findDistinctOwnerIds(int $badgeId): array
    {
        return $this->repository->findDistinctOwnerIds($badgeId);
    }

    /**
     * Check whether the given user is an owner of the badge.
     * @param int $badgeId
     * @param int $userId
     * @return bool
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
     * @param int $badgeId
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
     * @param int $badgeId
     * @param int $userId
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
     * @param int $badgeId
     * @param string|null $requestedLang
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
     * @param int $badgeId
     * @param string|null $requestedLang
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
     * @param int $badgeId
     */
    public function findLevels(int $badgeId): array
    {
        return $this->repository->findLevels($badgeId);
    }

    /**
     * Read recommended caches for a badge.
     *
     * @return array<int, array<string, mixed>>
     * @param int $badgeId
     */
    public function findRecommendedCaches(int $badgeId): array
    {
        return $this->repository->findRecommendedCaches($badgeId);
    }

    /**
     * Read caches of the week for a badge.
     *
     * @return array<int, array<string, mixed>>
     * @param int $badgeId
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
     * @param int $badgeId
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
     * @param int $badgeId
     * @param int $userId
     */
    public function getUserBadgeProgress(int $badgeId, int $userId): ?array
    {
        return $this->repository->findUserBadgeProgress($badgeId, $userId);
    }

    /**
     * Read a user's progress rows for all badges.
     *
     * @return array<int, array<string, mixed>>
     * @param int $userId
     */
    public function getUserBadgesProgress(int $userId): array
    {
        return $this->repository->findUserBadgesProgress($userId);
    }

    /**
     * Convert an integer to Roman numerals.
     */
    /**
     * Compute progress percentage within the current level.
     * @param int $currVal
     * @param int $prevVal
     * @param int $nextVal
     * @return int
     */
    public function getBadgeProgressPercent(int $currVal, int $prevVal, int $nextVal): int
    {
        if ($nextVal >= 999999 || $nextVal <= $prevVal) {
            return 100;
        }
        $range = $nextVal - $prevVal;
        $done = $currVal - $prevVal;
        if ($done < 0) {
            $done = 0;
        }
        if ($done > $range) {
            $done = $range;
        }
        return (int) round(($done / $range) * 100);
    }

    public function toRoman(int $num): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
        ];
        $result = '';
        foreach ($map as $roman => $value) {
            while ($num >= $value) {
                $result .= $roman;
                $num -= $value;
            }
        }
        return $result;
    }

    /**
     * Build the short description for a badge, using translation key if type is set.
     *
     * @param int $badgeId Badge id.
     * @param int $currVal Current value.
     * @param int $levelId Current level id.
     * @return string|null
     */
    public function getShortDescription(int $badgeId, int $currVal, int $levelId): ?string
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return null;
        }

        $levels = $this->findLevels($badgeId);
        $nextVal = 0;
        $foundCurrent = false;
        foreach ($levels as $level) {
            if ($foundCurrent) {
                $nextVal = (int) ($level['threshold'] ?? 0);
                break;
            }
            if ((int) ($level['level'] ?? 0) === $levelId) {
                $foundCurrent = true;
            }
        }
        if ($nextVal === 0) {
            $nextVal = 999999;
        }

        $typeId = (int) ($badge['short_description_type'] ?? 0);
        if ($typeId <= 0) {
            return null;
        }

        $typeRow = $this->repository->findShortDescriptionType($typeId);
        $key = is_array($typeRow) ? (string) ($typeRow['translation_key'] ?? '') : '';
        if ($key === '') {
            return null;
        }

        $remaining = $nextVal - $currVal;
        if ($remaining < 0) {
            $remaining = 0;
        }
        if ($nextVal >= 999999) {
            return tr('merit_badge_gain_max_level');
        }
        return tr($key, [$remaining]);
    }

    /**
     * Recalculate a user's progress for a badge and update badge_user.
     *
     * @return array{badgeId:int,userId:int,levelId:int,currVal:int,changed:bool}
     * @param int $badgeId
     * @param int $userId
     */
    public function recalculateUserProgress(int $badgeId, int $userId): array
    {
        $stats = $this->getBadgeStats($badgeId, $userId);
        $currVal = (int) ($stats['gainedCount'] ?? 0);

        $levels = $this->findLevels($badgeId);
        $newLevelId = 0;
        $nextVal = 0;
        $levelsCount = count($levels);

        for ($i = 0; $i < $levelsCount; $i++) {
            $level = $levels[$i];
            $threshold = (int) ($level['threshold'] ?? 0);
            if ($currVal >= $threshold) {
                $newLevelId = (int) ($level['level'] ?? 0);
                $nextVal = isset($levels[$i + 1]) ? (int) ($levels[$i + 1]['threshold'] ?? 0) : 999999;
            } else {
                $nextVal = $threshold;
                break;
            }
        }

        if ($levelsCount === 0) {
            $nextVal = 0;
        }

        $existing = $this->repository->findUserBadgeProgress($badgeId, $userId);
        $changed = true;
        $levelDate = date('Y-m-d');

        if ($existing !== null) {
            $oldLevel = (int) ($existing['level_id'] ?? 0);
            $changed = $newLevelId !== $oldLevel;
            if (! $changed) {
                $levelDate = (string) ($existing['level_date'] ?? date('Y-m-d'));
            }
        }

        if ($existing === null) {
            $this->repository->insertUserBadgeProgress($badgeId, $userId, $currVal, $newLevelId, $levelDate, $nextVal);
        } else {
            $this->repository->updateUserBadgeProgress(
                $badgeId,
                $userId,
                $currVal,
                $newLevelId,
                $changed ? $levelDate : null,
                $nextVal
            );
        }

        return [
            'badgeId' => $badgeId,
            'userId' => $userId,
            'levelId' => $newLevelId,
            'currVal' => $currVal,
            'nextVal' => $nextVal,
            'changed' => $changed,
        ];
    }

    /**
     * Compute ring CSS classes for a user's badge level.
     * Colour comes from the badge-level-* SCSS classes.
     *
     * @return array{colorClass: string, thickness: string}
     * @param int $levelId
     * @param int $levelsCount
     */
    public function getBadgeRingStyle(int $levelId, int $levelsCount): array
    {
        $idx = 1;
        if ($levelId > 0) {
            $idx = (int) round(($levelId + 1) * 10 / max(1, $levelsCount));
            if ($idx > 10) {
                $idx = 10;
            }
            if ($idx <= 0) {
                $idx = 1;
            }
        }

        $thickness = RingThicknessEnum::THICK->value;
        if ($levelId > 0 && $levelId < 4) {
            $thickness = RingThicknessEnum::THIN->value;
        } elseif ($levelId >= 4 && $levelId < 7) {
            $thickness = RingThicknessEnum::MEDIUM->value;
        }

        return [
            'colorClass' => 'badge-level-' . $idx,
            'thickness' => $thickness,
        ];
    }

    public function findBadgeLevelGainers(int $badgeId, int $level, int $page = 1, int $perPage = 20): array
    {
        return $this->repository->findBadgeLevelGainers($badgeId, $level, $page, $perPage);
    }

    /**
     * Read a badge by its id.
     *
     * @param int $badgeId Badge id.
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
                !empty($category['sort_desc'])
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
