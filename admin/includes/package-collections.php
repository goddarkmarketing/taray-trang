<?php
declare(strict_types=1);

/** @return array<string, array{key: string, label: string, listHref: string}> */
function tt_package_collections(): array
{
    return [
        'programs' => [
            'key' => 'programs',
            'label' => 'ไปเช้าเย็นกลับ',
            'listHref' => 'programs.php#daytrip',
        ],
        'packages2d1n' => [
            'key' => 'packages2d1n',
            'label' => '2 วัน 1 คืน',
            'listHref' => 'programs.php#overnight',
        ],
        'packages3d2n' => [
            'key' => 'packages3d2n',
            'label' => '3 วัน 2 คืน',
            'listHref' => 'programs.php#packages3d2n',
        ],
    ];
}

function tt_package_collection_key(?string $raw): string
{
    $allowed = array_keys(tt_package_collections());
    $key = trim((string)$raw);
    return in_array($key, $allowed, true) ? $key : 'programs';
}

/** @return list<array<string, mixed>> */
function tt_package_list(string $collection, ?array $data = null): array
{
    $data ??= tt_read_data();
    return $data[$collection] ?? [];
}

function tt_package_find(string $collection, string $id, ?array $data = null): array
{
    $list = tt_package_list($collection, $data);
    foreach ($list as $i => $item) {
        if (($item['id'] ?? '') === $id) {
            return ['item' => $item, 'index' => $i];
        }
    }
    return ['item' => null, 'index' => -1];
}

function tt_package_save_item(string $collection, array $item, int $idx, ?array $data = null): bool
{
    $data ??= tt_read_data();
    $list = $data[$collection] ?? [];
    if ($idx >= 0) {
        $list[$idx] = $item;
    } else {
        $list[] = $item;
    }
    $data[$collection] = array_values($list);
    return tt_write_data($data);
}

function tt_package_delete(string $collection, string $id, ?array $data = null): bool
{
    $data ??= tt_read_data();
    $list = array_values(array_filter(
        $data[$collection] ?? [],
        fn($p) => ($p['id'] ?? '') !== $id
    ));
    $data[$collection] = $list;
    return tt_write_data($data);
}
