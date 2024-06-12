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

        if ($row['lokasi'] == 'Head Office') {
            $id2 = '01';
        } elseif ($row['lokasi'] == 'Office Kendari') {
            $id2 = '02';
        } elseif ($row['lokasi'] == 'Site Molore') {
            $id2 = '03';
        }

        if ($row['kategori'] == 'Kendaraan') {
            $id3 = '01';
        } elseif ($row['kategori'] == 'Mesin') {
            $id3 = '02';
        } elseif ($row['kategori'] == 'Alat Berat') {
            $id3 = '03';
        } elseif ($row['kategori'] == 'Alat Lab') {
            $id3 = '04';
        } elseif ($row['kategori'] == 'Alat Preparasi') {
            $id3 = '05';
        } elseif ($row['kategori'] == 'Peralatan') {
            $id3 = '06';
        } else {
            $id3 = '07'; // Default code if no matching category is found
        }

        // Fetch last iteration value from the database
        $lastAsset = Inventory::orderBy('id', 'desc')->first();
        $iteration = $lastAsset ? $lastAsset->id + 1 : 1; // Start from 1 if no data
        $iteration = str_pad($iteration, 4, '0', STR_PAD_LEFT); // Ensure 4 digits with padding

        $id = $id1 . ' ' . $id2 . '-' . $id3;

        $ids = Inventory::where('asset_code', 'LIKE', "%$id%")->get();

        if ($ids->isNotEmpty()) {
            $dataCount = $ids->count();
            $iteration = str_pad($dataCount + 1, 4, '0', STR_PAD_LEFT);
            $id = $id1 . ' ' . $id2 . '-' . $id3 . '-' . $iteration;
        } else {
            $id = $id1 . ' ' . $id2 . '-' . $id3 . '-' . $iteration;
        }

        // dd($row);
        
        inventory::create([
            'old_asset_code' => $row['kode_asset_lama'],
            'location' => $row['lokasi'],
            'asset_category' => $row['kategori'],
            'asset_position_dept' => $row['asset_position'],
            'asset_type' => $row['jenis'],
            'description' => $row['deskripsi'],
            'serial_number' => $row['serial_number'],
            'acquisition_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_perolehan']),
            'useful_life' => $row['umur_ekonomis_tahun'],
            'acquisition_value' => $row['nilai_perolehan'],
            'pic_dept' => $pic_dept,
            'asset_code' => $id,
        ]);
    }
}
