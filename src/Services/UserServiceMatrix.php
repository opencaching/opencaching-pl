<?php

declare(strict_types=1);

namespace src\Services;

use src\Models\User\MatrixRepository;

/**
 * Prepares data for the user cache Matrix.
 */
class UserServiceMatrix
{
    private const LEVELS = [2, 3, 4, 5, 6, 7, 8, 9, 10];

    private readonly MatrixRepository $repository;

    public function __construct(?MatrixRepository $repository = null)
    {
        $this->repository = $repository ?? new MatrixRepository();
    }

    /**
     * Build a complete difficulty/terrain matrix, including zero values.
     *
     * @return array{
     *     columnLabels: array<int, string>,
     *     rows: array<int, array{label: string, counts: array<int, int>}>
     * }
     */
    public function getDifficultyTerrainMatrix(int $userId): array
    {
        $counts = [];

        foreach (self::LEVELS as $difficulty) {
            $counts[$difficulty] = array_fill_keys(self::LEVELS, 0);
        }

        foreach ($this->repository->findDifficultyTerrainCounts($userId) as $row) {
            $difficulty = (int) $row['difficulty'];

            if (! isset($counts[$difficulty])) {
                continue;
            }

            foreach (self::LEVELS as $terrain) {
                $counts[$difficulty][$terrain] = (int) ($row['terrain_' . $terrain] ?? 0);
            }
        }

        $rows = [];

        foreach ($counts as $difficulty => $terrainCounts) {
            $rows[] = [
                'label' => self::levelLabel($difficulty),
                'counts' => array_values($terrainCounts),
            ];
        }

        return [
            'columnLabels' => array_map(
                static fn (int $level): string => self::levelLabel($level),
                self::LEVELS
            ),
            'rows' => $rows,
        ];
    }

    private static function levelLabel(int $level): string
    {
        return number_format($level / 2, 1, '.', '');
    }
}
