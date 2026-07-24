<?php

namespace App\Services;

use App\Models\inventory;
use Illuminate\Validation\ValidationException;

class AssetCodeGenerator
{
    private const LOCATION_CODES = [
        'Head Office' => '01',
        'Office Kendari' => '02',
        'Site Molore' => '03',
    ];

    private const CATEGORY_CODES = [
        'Kendaraan' => '01',
        'Peralatan' => '02',
        'Bangunan' => '03',
        'Mesin' => '04',
        'Alat Berat' => '05',
        'Alat Lab & Preparasi' => '06',
    ];

    public function attributes(string $company, float|int|null $acquisitionValue, string $location, string $category): array
    {
        $company = strtoupper(trim($company));
        $locationCode = self::LOCATION_CODES[$location] ?? null;
        $categoryCode = self::CATEGORY_CODES[$category] ?? null;

        if (! in_array($company, ['MLP', 'KES'], true) || ! $locationCode || ! $categoryCode) {
            throw ValidationException::withMessages([
                'asset_code' => 'Company, location, or asset category cannot be used to generate an asset code.',
            ]);
        }

        $departmentCode = (float) $acquisitionValue > 2499999 ? 'FG' : 'GA';
        $prefix = ($company === 'KES' ? 'KES ' : '') . $departmentCode . ' ' . $locationCode . '-' . $categoryCode;

        return [
            'company' => $company,
            'pic_dept' => $departmentCode === 'FG' ? 'FAT & GA' : 'GA',
            'prefix' => $prefix,
        ];
    }

    public function generate(string $company, float|int|null $acquisitionValue, string $location, string $category): array
    {
        $attributes = $this->attributes($company, $acquisitionValue, $location, $category);
        $existingCodes = inventory::query()
            ->where('company', $attributes['company'])
            ->where('asset_code', 'like', $attributes['prefix'] . '-%')
            ->pluck('asset_code');

        $numbers = $existingCodes
            ->map(fn (string $code) => (int) last(explode('-', $code)))
            ->filter(fn (int $number) => $number > 0)
            ->all();

        $sequence = empty($numbers) ? 1 : max($numbers) + 1;

        return $attributes + [
            'asset_code' => $attributes['prefix'] . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
        ];
    }
}
