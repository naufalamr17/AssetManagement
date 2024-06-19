<?php

namespace App\Imports;

use App\Models\inventory;
use App\Models\YourModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class YourDataImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Define PIC Dept and other ID components based on the acquisition_value
        if ($row['nilai_perolehan'] > 2500000) {
            $pic_dept = 'FAT & GA';
            $id1 = 'FG';
        } else {
            $pic_dept = 'GA';
            $id1 = 'GA';
        }

        if (ucwords(strtolower($row['lokasi'])) == 'Head Office') {
            $id2 = '01';
        } elseif (ucwords(strtolower($row['lokasi'])) == 'Office Kendari') {
            $id2 = '02';
        } elseif (ucwords(strtolower($row['lokasi'])) == 'Site Molore') {
            $id2 = '03';
        }

        if (ucwords(strtolower($row['kategori'])) == 'Kendaraan') {
            $id3 = '01';
            $useful_life = 8;
        } elseif (ucwords(strtolower($row['kategori'])) == 'Peralatan') {
            $id3 = '02';
            $useful_life = 4;
        } elseif (ucwords(strtolower($row['kategori'])) == 'Bangunan') {
            $id3 = '03';
            $useful_life = 20;
        } elseif (ucwords(strtolower($row['kategori'])) == 'Mesin') {
            $id3 = '04';
            $useful_life = 16;
        } elseif (ucwords(strtolower($row['kategori'])) == 'Alat Berat') {
            $id3 = '05';
            $useful_life = 8;
        } elseif (ucwords(strtolower($row['kategori'])) == 'Alat Lab & Preparasi') {
            $id3 = '06';
            $useful_life = 16;
        }

        // Fetch last iteration value from the database
        // $lastAsset = Inventory::orderBy('id', 'desc')->first();
        // $iteration = $lastAsset ? $lastAsset->id + 1 : 1; // Start from 1 if no data
        // $iteration = str_pad($iteration, 4, '0', STR_PAD_LEFT); // Ensure 4 digits with padding

        $id = $id1 . ' ' . $id2 . '-' . $id3;

        $ids = Inventory::where('asset_code', 'LIKE', "%$id%")->get();
        // dd($ids);

        if ($ids->isNotEmpty()) {
            $dataCount = $ids->count();
            $iteration = str_pad($dataCount + 1, 4, '0', STR_PAD_LEFT);
            $id = $id1 . ' ' . $id2 . '-' . $id3 . '-' . $iteration;
        } else {
            $dataCount = $ids->count();
            $iteration = str_pad($dataCount + 1, 4, '0', STR_PAD_LEFT);
            $id = $id1 . ' ' . $id2 . '-' . $id3 . '-' . $iteration;
        }

        // dd($row);

        inventory::create([
            'old_asset_code' => $row['kode_asset_lama'],
            'location' => ucwords(strtolower($row['lokasi'])),
            'asset_category' => ucwords(strtolower($row['kategori'])),
            'asset_position_dept' => $row['asset_position'],
            'asset_type' => $row['jenis'],
            'description' => $row['deskripsi'],
            'serial_number' => $row['serial_number'],
            'acquisition_date' => $row['tanggal_perolehan'],
            'useful_life' => $useful_life,
            'acquisition_value' => $row['nilai_perolehan'],
            'pic_dept' => $pic_dept,
            'asset_code' => $id,
        ]);
    }
}
