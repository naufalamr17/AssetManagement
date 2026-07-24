<?php

namespace App\Imports;

use App\Models\inventory;
use App\Models\YourModel;
use App\Services\AssetCodeGenerator;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class YourDataImport implements ToModel, WithHeadingRow
{
    public function __construct(private readonly string $company = 'MLP')
    {
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $location = ucwords(strtolower($row['lokasi']));
        $category = ucwords(strtolower($row['kategori']));
        $usefulLifeByCategory = [
            'Kendaraan' => 8,
            'Peralatan' => 4,
            'Bangunan' => 20,
            'Mesin' => 16,
            'Alat Berat' => 8,
            'Alat Lab & Preparasi' => 16,
        ];
        $code = app(AssetCodeGenerator::class)->generate($this->company, $row['nilai_perolehan'] ?? 0, $location, $category);

        // dd($row);

        inventory::create([
            'old_asset_code' => $row['kode_asset_lama'],
            'company' => $code['company'],
            'location' => $location,
            'asset_category' => $category,
            'asset_position_dept' => $row['asset_position'],
            'merk' => $row['merk'],
            'asset_type' => $row['jenis'],
            'description' => $row['deskripsi'],
            'serial_number' => $row['serial_number'],
            'acquisition_date' => $row['tanggal_perolehan'],
            'useful_life' => $usefulLifeByCategory[$category],
            'acquisition_value' => $row['nilai_perolehan'],
            'status' => $row['status'],
            'pic_dept' => $code['pic_dept'],
            'asset_code' => $code['asset_code'],
            'user' => $row['user'],
            'dept' => $row['dept'],
        ]);
    }
}
