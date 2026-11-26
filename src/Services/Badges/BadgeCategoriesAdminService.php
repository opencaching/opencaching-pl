<?php

namespace src\Services\Badges;

use RuntimeException;
use src\Enums\BadgeCategorySortEnum;
use src\Models\Badges\BadgesRepository;

/**
 * Service for badge category admin operations.
 */
class BadgeCategoriesAdminService
{
    private BadgesRepository $repository;

    public function __construct(?BadgesRepository $repository = null)
    {
        $this->repository = $repository ?? new BadgesRepository();
    }

    /**
     * Read paginated badge categories for the admin panel.
     *
     * @return array{items: array<int, array<string, mixed>>, total: int}
     * @param string|null $name
     * @param int|null $id
     * @param BadgeCategorySortEnum $sort
     * @param int $page
     * @param int $perPage
     */
    public function getCategories(?string $name, ?int $id, BadgeCategorySortEnum $sort, int $page, int $perPage): array
    {
        $total = $this->repository->countCategoriesAdmin($name, $id);
        $items = $this->repository->findAllCategoriesAdmin($name, $id, $sort->sqlOrder(), $page, $perPage);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Read a single badge category by id.
     *
     * @return array<string, mixed>|null
     * @param int $id
     */
    public function getCategory(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        return $this->repository->findCategoryById($id);
    }

    /**
     * @param array{name:string,sequence:int,active:int,sort_desc:int} $data
     * @return int new/existing id
     * @throws RuntimeException on validation error
     */
    public function saveCategory(int $id, array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $sequence = (int) ($data['sequence'] ?? 0);
        if ($sequence < 0) {
            $sequence = 0;
        }
        if ($sequence > 1000) {
            $sequence = 1000;
        }
        $active = !empty($data['active']) ? 1 : 0;
        $sortDesc = !empty($data['sort_desc']) ? 1 : 0;

        if ($name === '') {
            throw new RuntimeException('form_error_field_is_required', 400);
        }
        if (mb_strlen($name) > 30) {
            throw new RuntimeException('form_error_field_too_long', 400);
        }

        return $this->repository->saveCategory($id, $name, $sequence, $active, $sortDesc);
    }
}
