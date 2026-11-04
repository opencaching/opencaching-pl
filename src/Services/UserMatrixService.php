<?php

declare(strict_types=1);

namespace src\Services;

use src\Models\User\MatrixRepository;

/**
 * Prepares data for the user cache Matrix.
 */
class UserMatrixService
{
    /**
     * Difficulty and terrain levels as stored in the database.
     * Each value represents half a star, from 1.0 to 5.0.
     */
    private const LEVELS = [2, 3, 4, 5, 6, 7, 8, 9, 10];

    private readonly MatrixRepository $repository;

    /**
     * Create the service with an optional repository.
     *
     * @param MatrixRepository|null $repository Repository used for database access.
     */
    public function __construct(?MatrixRepository $repository = null)
    {
        $this->repository = $repository ?? new MatrixRepository();
    }

    /**
     * Build a complete difficulty/terrain matrix.
     *
     * The repository query may omit difficulty/terrain pairs without found caches.
     * Those missing combinations are filled with zero.
     *
     * @return array{
     *     columnLabels: array<int, string>,
     *     rows: array<int, array{label: string, counts: array<int, int>, total: int}>,
     *     columnTotals: array<int, int>,
     *     grandTotal: int,
     *     minimum: int
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
            $terrain = (int) $row['terrain'];

            if (
                ! isset($counts[$difficulty])
                || ! array_key_exists($terrain, $counts[$difficulty])
            ) {
                continue;
            }

            $counts[$difficulty][$terrain] = (int) $row['cache_count'];
        }

        $rows = [];
        $columnTotals = array_fill_keys(self::LEVELS, 0);

        foreach ($counts as $difficulty => $terrainCounts) {
            $rowTotal = 0;

            foreach (self::LEVELS as $terrain) {
                $rowTotal += $terrainCounts[$terrain];
                $columnTotals[$terrain] += $terrainCounts[$terrain];
            }

            $rows[] = [
                'label' => self::levelLabel($difficulty),
                'counts' => array_values($terrainCounts),
                'total' => $rowTotal,
            ];
        }

        $allCounts = [];

        foreach ($rows as $row) {
            foreach ($row['counts'] as $count) {
                $allCounts[] = $count;
            }
        }

        return [
            'columnLabels' => array_map(
                static fn (int $level): string => self::levelLabel($level),
                self::LEVELS
            ),
            'rows' => $rows,
            'columnTotals' => array_values($columnTotals),
            'grandTotal' => array_sum($columnTotals),
            'minimum' => $allCounts === [] ? 0 : min($allCounts),
        ];
    }

    /**
     * Format a difficulty or terrain level as a one-decimal string.
     */
    private static function levelLabel(int $level): string
    {
        return number_format($level / 2, 1, '.', '');
    }

    /**
     * Build a complete year/month matrix.
     *
     * Years without finds are still shown when they fall between the first and
     * last year with finds. Months without finds are filled with zero.
     *
     * @return array{
     *     columnLabels: array<int, string>,
     *     rows: array<int, array{label: string, values: array<int, int>, total: int}>,
     *     columnTotals: array<int, int>,
     *     grandTotal: int
     * }
     */
    public function getYearMonthMatrix(int $userId): array
    {
        $counts = [];
        $years = [];

        foreach ($this->repository->findYearMonthCounts($userId) as $row) {
            $year = (int) $row['year'];
            $month = (int) $row['month'];

            if ($year <= 0 || $month < 1 || $month > 12) {
                continue;
            }

            if (! isset($counts[$year])) {
                $counts[$year] = array_fill(1, 12, 0);
                $years[] = $year;
            }

            $counts[$year][$month] = (int) $row['cache_count'];
        }

        if ($years === []) {
            $years = [(int) date('Y')];
            $counts[$years[0]] = array_fill(1, 12, 0);
        }

        sort($years);

        $allYears = range($years[0], $years[count($years) - 1]);
        $rows = [];
        $columnTotals = array_fill(1, 12, 0);

        foreach ($allYears as $year) {
            if (! isset($counts[$year])) {
                $counts[$year] = array_fill(1, 12, 0);
            }

            $rowTotal = array_sum($counts[$year]);

            foreach (range(1, 12) as $month) {
                $columnTotals[$month] += $counts[$year][$month];
            }

            $rows[] = [
                'label' => (string) $year,
                'values' => array_values($counts[$year]),
                'total' => $rowTotal,
            ];
        }

        return [
            'columnLabels' => array_map(
                static fn (int $month): string => date('F', mktime(0, 0, 0, $month, 1)),
                range(1, 12)
            ),
            'rows' => $rows,
            'columnTotals' => array_values($columnTotals),
            'grandTotal' => array_sum($columnTotals),
        ];
    }

    /**
     * Build a complete month/day matrix.
     *
     * Days that do not exist in a given month are represented by "X".
     *
     * @return array{
     *     columnLabels: array<int, string>,
     *     rows: array<int, array{label: string, values: array<int, int|string>, total: int}>,
     *     columnTotals: array<int, int>,
     *     grandTotal: int
     * }
     */
    public function getMonthDayMatrix(int $userId): array
    {
        $daysInMonth = [
            1 => 31,
            2 => 29,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31,
        ];

        $counts = [];

        foreach ($daysInMonth as $month => $dayCount) {
            for ($day = 1; $day <= $dayCount; $day++) {
                $counts[$month][$day] = 0;
            }
        }

        foreach ($this->repository->findMonthDayCounts($userId) as $row) {
            $month = (int) $row['month'];
            $day = (int) $row['day'];

            if (! isset($counts[$month][$day])) {
                continue;
            }

            $counts[$month][$day] = (int) $row['cache_count'];
        }

        $rows = [];
        $columnTotals = array_fill_keys(array_keys($daysInMonth), 0);

        for ($day = 1; $day <= 31; $day++) {
            $values = [];
            $rowTotal = 0;

            foreach ($daysInMonth as $month => $dayCount) {
                if ($day > $dayCount) {
                    $values[] = 'X';

                    continue;
                }

                $count = $counts[$month][$day];
                $values[] = $count;
                $rowTotal += $count;
                $columnTotals[$month] += $count;
            }

            $rows[] = [
                'label' => (string) $day,
                'values' => $values,
                'total' => $rowTotal,
            ];
        }

        return [
            'columnLabels' => array_map(
                static fn (int $month): string => date('F', mktime(0, 0, 0, $month, 1)),
                array_keys($daysInMonth)
            ),
            'rows' => $rows,
            'columnTotals' => array_values($columnTotals),
            'grandTotal' => array_sum($columnTotals),
        ];
    }
}
