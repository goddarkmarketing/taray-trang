<?php
declare(strict_types=1);

/** @param array<string, mixed> $row */
function tt_bb_clean_row(array $row, array $keys): array
{
    $out = [];
    foreach ($keys as $key) {
        $val = $row[$key] ?? '';
        if (is_string($val)) {
            $val = trim($val);
        }
        if ($val === '' || $val === null) {
            continue;
        }
        $out[$key] = $val;
    }
    return $out;
}

/** @return list<array<string, mixed>> */
function tt_bb_parse_routes_post(?array $rows): array
{
    $out = [];
    foreach ($rows ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = trim((string) ($row['id'] ?? ''));
        $name = trim((string) ($row['name'] ?? ''));
        if ($id === '' || $name === '') {
            continue;
        }
        $item = ['id' => $id, 'name' => $name];
        $subtitle = trim((string) ($row['subtitle'] ?? ''));
        $image = trim((string) ($row['image'] ?? ''));
        if ($subtitle !== '') {
            $item['subtitle'] = $subtitle;
        }
        if ($image !== '') {
            $item['image'] = $image;
        }
        $out[] = $item;
    }
    return $out;
}

/** @return list<array<string, mixed>> */
function tt_bb_parse_people_tiers_post(?array $rows): array
{
    $out = [];
    foreach ($rows ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = trim((string) ($row['id'] ?? ''));
        $label = trim((string) ($row['label'] ?? ''));
        if ($id === '' || $label === '') {
            continue;
        }
        $out[] = [
            'id' => $id,
            'min' => (int) ($row['min'] ?? 1),
            'max' => (int) ($row['max'] ?? 1),
            'label' => $label,
        ];
    }
    return $out;
}

/** @return list<array<string, mixed>> */
function tt_bb_parse_addons_post(?array $rows): array
{
    $out = [];
    foreach ($rows ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = trim((string) ($row['id'] ?? ''));
        $label = trim((string) ($row['label'] ?? ''));
        if ($id === '' || $label === '') {
            continue;
        }
        $unit = trim((string) ($row['unit'] ?? 'flat'));
        if ($unit === 'per_person') {
            $unit = 'perPerson';
        }
        $price = (int) ($row['price'] ?? 0);
        $item = [
            'id' => $id,
            'icon' => trim((string) ($row['icon'] ?? 'plus')) ?: 'plus',
            'label' => $label,
            'desc' => trim((string) ($row['desc'] ?? '')),
            'price' => $price,
            'unit' => $unit,
        ];
        $priceLabel = trim((string) ($row['priceLabel'] ?? ''));
        if ($priceLabel !== '') {
            $item['priceLabel'] = $priceLabel;
        }
        $out[] = $item;
    }
    return $out;
}

/** @return list<array<string, mixed>> */
function tt_bb_parse_sizes_post(?array $rows): array
{
    $out = [];
    foreach ($rows ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = trim((string) ($row['id'] ?? ''));
        $label = trim((string) ($row['label'] ?? ''));
        if ($id === '' || $label === '') {
            continue;
        }
        $item = [
            'id' => $id,
            'label' => $label,
            'capacity' => (int) ($row['capacity'] ?? 0),
        ];
        $capLabel = trim((string) ($row['capacityLabel'] ?? ''));
        if ($capLabel !== '') {
            $item['capacityLabel'] = $capLabel;
        }
        $out[] = $item;
    }
    return $out;
}

/**
 * @param array<string, array<string, int|string>> $matrixPost [routeId][tierId] => price
 * @return array<string, array<string, int>>
 */
function tt_bb_parse_charter_matrix_post(?array $matrixPost): array
{
    $out = [];
    foreach ($matrixPost ?? [] as $routeId => $tiers) {
        $routeId = trim((string) $routeId);
        if ($routeId === '' || !is_array($tiers)) {
            continue;
        }
        $row = [];
        foreach ($tiers as $tierId => $price) {
            $tierId = trim((string) $tierId);
            if ($tierId === '' || $price === '' || $price === null) {
                continue;
            }
            $row[$tierId] = (int) $price;
        }
        if ($row) {
            $out[$routeId] = $row;
        }
    }
    return $out;
}

/** @param array<string, int|string> $counts */
function tt_bb_parse_guide_counts_post(?array $counts): array
{
    $out = [];
    foreach ($counts ?? [] as $tierId => $count) {
        $tierId = trim((string) $tierId);
        if ($tierId === '' || $count === '' || $count === null) {
            continue;
        }
        $out[$tierId] = (int) $count;
    }
    return $out;
}

/** @return array<string, array{hideTiers?: list<string>, tierCaps?: array<string, int>}> */
function tt_bb_parse_tier_rules_post(?array $rulesPost): array
{
    $out = [];
    foreach ($rulesPost ?? [] as $routeId => $rule) {
        $routeId = trim((string) $routeId);
        if ($routeId === '' || !is_array($rule)) {
            continue;
        }
        $item = [];
        $hide = array_values(array_filter(array_map('trim', explode(',', (string) ($rule['hideTiers'] ?? '')))));
        if ($hide) {
            $item['hideTiers'] = $hide;
        }
        $caps = [];
        foreach ($rule['tierCaps'] ?? [] as $tierId => $cap) {
            $tierId = trim((string) $tierId);
            if ($tierId === '' || $cap === '' || $cap === null) {
                continue;
            }
            $caps[$tierId] = (int) $cap;
        }
        if ($caps) {
            $item['tierCaps'] = $caps;
        }
        if ($item) {
            $out[$routeId] = $item;
        }
    }
    return $out;
}

/** @return list<string> */
function tt_bb_boat_ids(array $data): array
{
    return array_values(array_filter(array_map(
        static fn($b) => (string) ($b['id'] ?? ''),
        $data['boats'] ?? []
    )));
}

/** @return array<string, mixed> */
function tt_bb_get(array $data): array
{
    return is_array($data['boatBooking'] ?? null) ? $data['boatBooking'] : [];
}

function tt_bb_addon_unit_label(string $unit): string
{
    return $unit === 'perPerson' || $unit === 'per_person' ? 'ต่อท่าน' : 'ต่อลำ';
}

/** @param list<array<string, mixed>> $addons */
function tt_bb_find_addon_index(array $addons, string $id): int
{
    foreach ($addons as $i => $addon) {
        if (($addon['id'] ?? '') === $id) {
            return $i;
        }
    }
    return -1;
}

/** @return array<string, mixed>|null */
function tt_bb_parse_addon_post(array $row): ?array
{
    $parsed = tt_bb_parse_addons_post([$row]);
    return $parsed[0] ?? null;
}
