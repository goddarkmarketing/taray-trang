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
        if ($unit === 'per_person_split') {
            $unit = 'perPersonSplit';
        }
        if ($unit === 'per_boat') {
            $unit = 'perBoat';
        }
        if ($unit === 'per_van') {
            $unit = 'perVan';
        }
        if ($unit === 'per_qty') {
            $unit = 'perQty';
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
        if (isset($row['adultPrice']) && $row['adultPrice'] !== '') {
            $item['adultPrice'] = (int) $row['adultPrice'];
        }
        if (isset($row['childPrice']) && $row['childPrice'] !== '') {
            $item['childPrice'] = (int) $row['childPrice'];
        }
        $adultLabel = trim((string) ($row['adultLabel'] ?? ''));
        if ($adultLabel !== '') {
            $item['adultLabel'] = $adultLabel;
        }
        $childLabel = trim((string) ($row['childLabel'] ?? ''));
        if ($childLabel !== '') {
            $item['childLabel'] = $childLabel;
        }
        $priceLabel = trim((string) ($row['priceLabel'] ?? ''));
        if ($priceLabel !== '') {
            $item['priceLabel'] = $priceLabel;
        }
        $qtyLabel = trim((string) ($row['qtyLabel'] ?? ''));
        if ($qtyLabel !== '') {
            $item['qtyLabel'] = $qtyLabel;
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
    return match ($unit) {
        'perPerson', 'per_person' => 'ต่อท่าน (กรอกจำนวน)',
        'perPersonSplit', 'per_person_split' => 'แยกผู้ใหญ่/เด็ก (กรอกจำนวน)',
        'perBoat', 'per_boat' => 'ต่อลำ',
        'perVan', 'per_van' => 'ต่อคัน',
        'perQty', 'per_qty' => 'กรอกจำนวน',
        default => 'ต่อลำ (คงที่)',
    };
}

function tt_bb_render_addon_unit_select(string $name, string $selected): void
{
    $selected = match ($selected) {
        'per_person' => 'perPerson',
        'per_person_split' => 'perPersonSplit',
        'per_boat' => 'perBoat',
        'per_van' => 'perVan',
        'per_qty' => 'perQty',
        default => $selected,
    };
    $options = [
        'flat' => 'ต่อลำ (คงที่)',
        'perPerson' => 'ต่อท่าน (กรอกจำนวน)',
        'perPersonSplit' => 'แยกผู้ใหญ่/เด็ก (กรอกจำนวน)',
        'perBoat' => 'ต่อลำ (คูณจำนวนเรือ)',
        'perVan' => 'ต่อคัน (กรอกจำนวน)',
        'perQty' => 'กรอกจำนวน (ไกด์/ช่างภาพ)',
    ];
    ?>
<select name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
  <?php foreach ($options as $value => $label): ?>
  <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"<?= $selected === $value ? ' selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
  <?php endforeach; ?>
</select>
<?php
}

/** @param array<string, mixed> $profile */
function tt_bb_set_profile_string(array &$profile, string $key, string $value): void
{
    $value = trim($value);
    if ($value === '') {
        unset($profile[$key]);
    } else {
        $profile[$key] = $value;
    }
}

/**
 * @param array<string, array<string, int|string>> $post
 * @param list<string> $sizeIds
 * @param list<string> $addonIds
 * @return array<string, array<string, int>>
 */
function tt_bb_parse_addon_qty_by_size_post(?array $post, array $sizeIds, array $addonIds): array
{
    $out = [];
    foreach ($sizeIds as $sizeId) {
        $sizeId = trim($sizeId);
        if ($sizeId === '') {
            continue;
        }
        $row = $post[$sizeId] ?? [];
        if (!is_array($row)) {
            continue;
        }
        $items = [];
        foreach ($addonIds as $addonId) {
            $addonId = trim($addonId);
            if ($addonId === '') {
                continue;
            }
            $qty = $row[$addonId] ?? '';
            if ($qty === '' || $qty === null) {
                continue;
            }
            $items[$addonId] = max(0, (int) $qty);
        }
        if ($items) {
            $out[$sizeId] = $items;
        }
    }
    return $out;
}

/** @param list<array<string, mixed>> $addons @return list<string> */
function tt_bb_qty_default_addon_ids(array $addons): array
{
    $ids = [];
    foreach ($addons as $addon) {
        $unit = (string) ($addon['unit'] ?? 'flat');
        if (in_array($unit, ['perQty', 'per_qty', 'perVan', 'per_van'], true)) {
            $id = trim((string) ($addon['id'] ?? ''));
            if ($id !== '') {
                $ids[] = $id;
            }
        }
    }
    return $ids;
}

/**
 * @param array<string, mixed> $existing
 * @param array<string, mixed> $post
 * @param array<string, mixed> $bb
 */
function tt_bb_merge_profile_post(array $existing, array $post, array $bb, string $boatId): array
{
    $profile = $existing;
    $selectionMode = trim((string) ($post['selectionMode'] ?? 'people')) === 'size' ? 'size' : 'people';
    $profile['selectionMode'] = $selectionMode;

    foreach (['tierProgressLabel', 'tierStepTitle', 'tierStepDesc', 'charterLabel', 'charterSummaryPrefix', 'includedBoxTitle', 'footerNote'] as $key) {
        tt_bb_set_profile_string($profile, $key, (string) ($post[$key] ?? ''));
    }

    foreach (['quoteTitlePrefix', 'quoteBoatName', 'quoteCharterName', 'insuranceQuoteLabel', 'defaultSizeId'] as $key) {
        tt_bb_set_profile_string($profile, $key, (string) ($post[$key] ?? ''));
    }

    $profile['insurancePerPerson'] = (int) ($post['insurancePerPerson'] ?? ($bb['insurancePerPerson'] ?? 50));
    $profile['guideFee'] = (int) ($post['guideFee'] ?? 0);
    $profile['safetyStaffRate'] = (int) ($post['safetyStaffRate'] ?? 0);
    $profile['safetyStaffRatio'] = max(1, (int) ($post['safetyStaffRatio'] ?? 20));

    $profile['askPax'] = isset($post['askPax']);
    foreach (['multiBoat', 'splitPax', 'itemizedQuote', 'autoSizeFromPax'] as $flag) {
        if (isset($post[$flag])) {
            $profile[$flag] = true;
        } else {
            unset($profile[$flag]);
        }
    }

    $capacityPerBoat = (int) ($post['capacityPerBoat'] ?? 0);
    if (isset($post['multiBoat']) && $capacityPerBoat > 0) {
        $profile['capacityPerBoat'] = $capacityPerBoat;
    } else {
        unset($profile['capacityPerBoat']);
    }

    if ($selectionMode === 'size') {
        $sizes = tt_bb_parse_sizes_post($post['sizes'] ?? []);
        if ($sizes) {
            $profile['sizes'] = $sizes;
        }
    }

    $matrix = tt_bb_parse_charter_matrix_post($post['charter_prices'] ?? []);
    if ($matrix) {
        $profile['charterPrices'] = $matrix;
    }

    $guideCounts = tt_bb_parse_guide_counts_post($post['guideCountByTier'] ?? []);
    if ($guideCounts) {
        $profile['guideCountByTier'] = $guideCounts;
    } else {
        unset($profile['guideCountByTier']);
    }

    $addons = tt_bb_parse_addons_post($post['addons'] ?? []);
    $profile['addons'] = $addons;

    $included = tt_parse_lines((string) ($post['includedItems'] ?? ''));
    if ($included) {
        $profile['includedItems'] = $included;
    } else {
        unset($profile['includedItems']);
    }

    $profile['routeIds'] = array_values(array_filter(array_map(
        static fn($id) => trim((string) $id),
        $post['routeIds'] ?? []
    )));

    $tierRules = tt_bb_parse_tier_rules_post($post['tierRules'] ?? []);
    if ($tierRules) {
        $profile['tierRules'] = $tierRules;
    } else {
        unset($profile['tierRules']);
    }

    if ($selectionMode === 'size') {
        $sizeIds = array_values(array_filter(array_map(
            static fn($row) => trim((string) ($row['id'] ?? '')),
            $profile['sizes'] ?? []
        )));
        $addonIds = tt_bb_qty_default_addon_ids($addons);
        if ($addonIds) {
            $addonQtyBySize = tt_bb_parse_addon_qty_by_size_post($post['addonQtyBySize'] ?? [], $sizeIds, $addonIds);
            if ($addonQtyBySize) {
                $profile['addonQtyBySize'] = $addonQtyBySize;
            } else {
                unset($profile['addonQtyBySize']);
            }
        } else {
            unset($profile['addonQtyBySize']);
        }
    } else {
        unset($profile['addonQtyBySize']);
        unset($profile['autoSizeFromPax']);
        unset($profile['defaultSizeId']);
    }

    return $profile;
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
