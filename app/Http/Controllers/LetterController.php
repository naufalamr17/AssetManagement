<?php

namespace App\Http\Controllers;

use App\Models\FormKerusakan;
use App\Models\inventory;
use Illuminate\Http\Request;
use App\Models\Letter;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class LetterController extends Controller
{
    public function generate(Request $request)
    {
        if ($request->ajax()) {
            $data = Letter::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (Auth::user()->status == 'Administrator' || Auth::user()->status == 'Super Admin') {
                        $btn .= '<a href="javascript:void(0)" class="edit btn btn-dark btn-sm mt-3"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" class="delete btn btn-danger btn-sm mt-3"><i class="fas fa-trash-alt"></i></a>';
                    }
                    $btn .= ' <a href="' . route('letters.download', $row->id) . '" class="btn btn-info btn-sm mt-3"><i class="fas fa-download"></i></a>';
                    return $btn;
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator;
                })
                ->addColumn('location', function ($row) {
                    return $row->location;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.letter.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'perihal' => 'nullable|string|max:255',
            'jenisBA' => 'required|string|max:255',
        ]);

        $perihal = $request->perihal ?? '-';

        // Generate kode_surat based on jenisBA
        $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('dm');
        $tahun = \Carbon\Carbon::parse($request->tanggal)->format('Y');
        $jenisBA = $request->jenisBA;
        $kode_surat = '';

        if ($jenisBA == 'FORM KERUSAKAN ASSET') {
            $bulan = \Carbon\Carbon::parse($request->tanggal)->format('m');
            // Get the latest iterasi for FORM PENGHAPUSAN ASSET for the current year
            $latestLetter = Letter::whereYear('tanggal', $tahun)
                ->where('jenisBA', 'FORM KERUSAKAN ASSET')
                ->orderBy('id', 'desc')
                ->first();
            if ($latestLetter) {
                $latestIterasi = intval(explode('_', $latestLetter->kode_surat)[0]);
                $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $iterasi = '001';
            }
            $kode_surat = "{$iterasi}_FKKA_GA-MLP/{$bulan}/{$tahun}";
        } else {
            // Get the latest iterasi for BA for the current year
            $latestLetter = Letter::whereYear('tanggal', $tahun)
                ->where('jenisBA', '!=', 'FORM KERUSAKAN ASSET')
                ->orderBy('id', 'desc')
                ->first();
            if ($latestLetter) {
                $latestIterasi = intval(explode('/', $latestLetter->kode_surat)[0]);
                $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $iterasi = '001';
            }
            $kode_surat = "{$iterasi}/BA/{$tanggal}/{$tahun}/";

            switch ($jenisBA) {
                case 'ASSET HILANG':
                    $kode_surat .= 'AH';
                    break;
                default:
                    $kode_surat .= 'AST'; // Default case if needed
                    break;
            }
        }

        Letter::create([
            'tanggal' => $request->tanggal,
            'perihal' => $perihal,
            'jenisBA' => $request->jenisBA,
            'kode_surat' => $kode_surat,
            'creator' => Auth::user()->name,
            'location' => Auth::user()->location,
        ]);

        return redirect()->route('generate-letter')->with('success', 'Data has been added successfully.');
    }

    public function edit($id)
    {
        $letter = Letter::findOrFail($id);
        return response()->json($letter);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'perihal' => 'nullable|string|max:255',
            'jenisBA' => 'required|string|max:255',
        ]);

        $perihal = $request->perihal ?? '-';

        $letter = Letter::findOrFail($id);

        // Update only the last two digits of kode_surat based on jenisBA
        $kode_surat = $letter->kode_surat;
        $kode_surat_parts = explode('/', $kode_surat);
        switch ($request->jenisBA) {
            case 'ASSET RUSAK':
                $kode_surat_parts[4] = 'AR';
                break;
            case 'ASSET DISPOSE':
                $kode_surat_parts[4] = 'AD';
                break;
            case 'ASSET HILANG':
                $kode_surat_parts[4] = 'AH';
                break;
            default:
                $kode_surat_parts[4] = 'AST'; // Default case if needed
                break;
        }
        $kode_surat = implode('/', $kode_surat_parts);

        $letter->update([
            'tanggal' => $request->tanggal,
            'perihal' => $perihal,
            'jenisBA' => $request->jenisBA,
            'kode_surat' => $kode_surat,
        ]);

        return redirect()->route('generate-letter')->with('success', 'Data has been updated successfully.');
    }

    public function destroy($id)
    {
        Letter::findOrFail($id)->delete();
        return response()->json(['success' => 'Data has been deleted successfully.']);
    }

    public function download($id)
    {
        $letter = Letter::findOrFail($id);

        $beritaAcara = $letter->beritaAcara;

        $formKerusakan = $letter->formKerusakan()->first();

        // dd($letter, $beritaAcara, $formKerusakan);

        if ($letter->jenisBA == 'ASSET SERAH TERIMA') {
            if ($letter->perihal == 'PEMINJAMAN ASSET') {
                if ($beritaAcara->isNotEmpty()) {
                    Carbon::setLocale('id');

                    $date = Carbon::parse($letter->tanggal);
                    $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                    $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                    $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                    $year = $date->format('Y'); // Year, e.g., 2023

                    $templatePath = storage_path('app/public/templates/PEMINJAMAN.docx');
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                    $templateProcessor->setValue('day', $day);
                    $templateProcessor->setValue('date', $dateFormatted);
                    $templateProcessor->setValue('month', $month);
                    $templateProcessor->setValue('year', $year);

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', $firstBeritaAcara->nama);
                    $templateProcessor->setValue('nik', $firstBeritaAcara->nik);
                    $templateProcessor->setValue('dept', $firstBeritaAcara->dept);
                    $templateProcessor->setValue('jabatan', $firstBeritaAcara->jabatan);
                    $templateProcessor->setValue('alamat', $firstBeritaAcara->alamat);

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", $asset->asset_code ?? '');
                            $templateProcessor->setValue("description#{$rowNumber}", $asset->description ?? '');
                            $templateProcessor->setValue("serial_number#{$rowNumber}", $asset->serial_number ?? '');
                            $templateProcessor->setValue("tanggal#{$rowNumber}", Carbon::parse($item->tanggal)->format('d-m-Y') ?? '');
                            $templateProcessor->setValue("alasan#{$rowNumber}", $item->alasan ?? '');
                        }
                    }

                    $templateProcessor->setValue('kronologi', $firstBeritaAcara->kronologi);

                    $tempFilePath = storage_path('app/BeritaAcara_' . $letter->id . '.docx');
                    $templateProcessor->saveAs($tempFilePath);

                    // Return the document as a download response
                    return response()->download($tempFilePath)->deleteFileAfterSend(true);
                } else {
                    return redirect()->route('form-berita-acara', ['id' => $id]);
                }
            } elseif ($letter->perihal == 'PENGEMBALIAN ASSET') {
                if ($beritaAcara->isNotEmpty()) {
                    Carbon::setLocale('id');

                    $date = Carbon::parse($letter->tanggal);
                    $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                    $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                    $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                    $year = $date->format('Y'); // Year, e.g., 2023

                    $templatePath = storage_path('app/public/templates/PENGEMBALIAN.docx');
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                    $templateProcessor->setValue('day', $day);
                    $templateProcessor->setValue('date', $dateFormatted);
                    $templateProcessor->setValue('month', $month);
                    $templateProcessor->setValue('year', $year);

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', $firstBeritaAcara->nama);
                    $templateProcessor->setValue('nik', $firstBeritaAcara->nik);
                    $templateProcessor->setValue('dept', $firstBeritaAcara->dept);
                    $templateProcessor->setValue('jabatan', $firstBeritaAcara->jabatan);
                    $templateProcessor->setValue('alamat', $firstBeritaAcara->alamat);

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", $asset->asset_code ?? '');
                            $templateProcessor->setValue("description#{$rowNumber}", $asset->description ?? '');
                            $templateProcessor->setValue("serial_number#{$rowNumber}", $asset->serial_number ?? '');
                            $templateProcessor->setValue("tanggal#{$rowNumber}", Carbon::parse($item->tanggal)->format('d-m-Y') ?? '');
                            $templateProcessor->setValue("alasan#{$rowNumber}", $item->alasan ?? '');
                        }
                    }

                    $templateProcessor->setValue('kronologi', $firstBeritaAcara->kronologi);

                    $tempFilePath = storage_path('app/BeritaAcara_' . $letter->id . '.docx');
                    $templateProcessor->saveAs($tempFilePath);

                    // Return the document as a download response
                    return response()->download($tempFilePath)->deleteFileAfterSend(true);
                } else {
                    return redirect()->route('form-berita-acara', ['id' => $id]);
                }
            } elseif ($letter->perihal == 'MUTASI ASSET') {
                if ($beritaAcara->isNotEmpty()) {
                    Carbon::setLocale('id');

                    $date = Carbon::parse($letter->tanggal);
                    $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                    $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                    $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                    $year = $date->format('Y'); // Year, e.g., 2023

                    $templatePath = storage_path('app/public/templates/MUTASI.docx');
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                    $templateProcessor->setValue('day', $day);
                    $templateProcessor->setValue('date', $dateFormatted);
                    $templateProcessor->setValue('month', $month);
                    $templateProcessor->setValue('year', $year);

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', $firstBeritaAcara->nama);
                    $templateProcessor->setValue('dept', $firstBeritaAcara->dept);
                    $templateProcessor->setValue('jabatan', $firstBeritaAcara->jabatan);

                    $templateProcessor->setValue('nama2', $firstBeritaAcara->nama_2);
                    $templateProcessor->setValue('dept2', $firstBeritaAcara->dept_2);
                    $templateProcessor->setValue('jabatan2', $firstBeritaAcara->jabatan_2);

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", $asset->asset_code ?? '');
                            $templateProcessor->setValue("description#{$rowNumber}", $asset->description ?? '');
                            $templateProcessor->setValue("serial_number#{$rowNumber}", $asset->serial_number ?? '');
                            $templateProcessor->setValue("tanggal#{$rowNumber}", Carbon::parse($item->tanggal)->format('d-m-Y') ?? '');
                            $templateProcessor->setValue("alasan#{$rowNumber}", $item->alasan ?? '');
                        }
                    }

                    $tempFilePath = storage_path('app/BeritaAcara_' . $letter->id . '.docx');
                    $templateProcessor->saveAs($tempFilePath);

                    // Return the document as a download response
                    return response()->download($tempFilePath)->deleteFileAfterSend(true);
                } else {
                    return redirect()->route('form-berita-acara', ['id' => $id]);
                }
            }
        } elseif ($letter->jenisBA == 'ASSET HILANG') {
            if ($beritaAcara->isNotEmpty()) {
                Carbon::setLocale('id');

                $date = Carbon::parse($letter->tanggal);
                $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                $year = $date->format('Y'); // Year, e.g., 2023

                $templatePath = storage_path('app/public/templates/HILANG.docx');
                $templateProcessor = new TemplateProcessor($templatePath);
                $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                $templateProcessor->setValue('day', $day);
                $templateProcessor->setValue('date', $dateFormatted);
                $templateProcessor->setValue('month', $month);
                $templateProcessor->setValue('year', $year);

                $firstBeritaAcara = $beritaAcara->first();
                $templateProcessor->setValue('nama', $firstBeritaAcara->nama);
                $templateProcessor->setValue('nik', $firstBeritaAcara->nik);
                $templateProcessor->setValue('dept', $firstBeritaAcara->dept);
                $templateProcessor->setValue('jabatan', $firstBeritaAcara->jabatan);
                $templateProcessor->setValue('alamat', $firstBeritaAcara->alamat);

                // Pastikan berita acara ada
                if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                    $rowCount = $beritaAcara->count(); // Hitung jumlah data
                    $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                    foreach ($beritaAcara as $index => $item) {
                        $asset = inventory::where('asset_code', $item->no_asset)->first();

                        // Indeks Word mulai dari 1, bukan 0
                        $rowNumber = $index + 1;

                        // Mengisi placeholder dengan data sesuai baris
                        $templateProcessor->setValue("kode_asset#{$rowNumber}", $asset->asset_code ?? '');
                        $templateProcessor->setValue("description#{$rowNumber}", $asset->description ?? '');
                        $templateProcessor->setValue("serial_number#{$rowNumber}", $asset->serial_number ?? '');
                        $templateProcessor->setValue("tanggal#{$rowNumber}", Carbon::parse($item->tanggal)->format('d-m-Y') ?? '');
                        $templateProcessor->setValue("alasan#{$rowNumber}", $item->alasan ?? '');
                    }
                }

                $templateProcessor->setValue('kronologi', $firstBeritaAcara->kronologi);

                $tempFilePath = storage_path('app/BeritaAcara_' . $letter->id . '.docx');
                $templateProcessor->saveAs($tempFilePath);

                // Return the document as a download response
                return response()->download($tempFilePath)->deleteFileAfterSend(true);
            } else {
                return redirect()->route('form-berita-acara', ['id' => $id]);
            }
        } elseif ($letter->jenisBA == 'FORM KERUSAKAN ASSET') {
            if ($letter->perihal == 'PENGGANTIAN ASSET') {
                if ($formKerusakan) {
                    Carbon::setLocale('id');

                    $date = Carbon::parse($letter->tanggal);
                    $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                    $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                    $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                    $year = $date->format('Y'); // Year, e.g., 2023

                    $asset = inventory::where('asset_code', $formKerusakan->kode_asset)->first();

                    $templatePath = storage_path('app/public/templates/PENGGANTIAN.docx');
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                    $templateProcessor->setValue('day', $day);
                    $templateProcessor->setValue('date', $dateFormatted);
                    $templateProcessor->setValue('month', $month);
                    $templateProcessor->setValue('year', $year);
                    $templateProcessor->setValue('kode_asset', $formKerusakan->kode_asset);
                    $templateProcessor->setValue('jenis', $asset->asset_type);
                    $templateProcessor->setValue('merk', $asset->merk);
                    $templateProcessor->setValue('deskripsi', $asset->description);
                    $templateProcessor->setValue('serial', $asset->serial_number);
                    $templateProcessor->setValue('tanggal_perolehan', $asset->acquisition_date);
                    $templateProcessor->setValue('kerusakan', $formKerusakan->kerusakan);
                    $templateProcessor->setValue('penyebab', $formKerusakan->penyebab);
                    $templateProcessor->setValue('harga_perolehan', 'Rp ' . number_format($asset->acquisition_value, 0, ',', '.'));
                    $templateProcessor->setValue('nama', $formKerusakan->nama);
                    $templateProcessor->setValue('nik', $formKerusakan->nik);
                    $templateProcessor->setValue('jabatan', $formKerusakan->jabatan);

                    // dd($templateProcessor);

                    $tempFilePath = storage_path('app/FormKerusakanAsset_' . $letter->id . '.docx');
                    $templateProcessor->saveAs($tempFilePath);

                    // Return the document as a download response
                    return response()->download($tempFilePath)->deleteFileAfterSend(true);
                } else {
                    return redirect()->route('form-kerusakan', ['id' => $id]);
                }
            } elseif ($letter->perihal == 'PERBAIKAN ASSET') {
                if ($formKerusakan) {
                    Carbon::setLocale('id');

                    $date = Carbon::parse($letter->tanggal);
                    $day = $date->translatedFormat('l'); // Full day name in Indonesian, e.g., Senin
                    $dateFormatted = $date->format('d'); // Day of the month, e.g., 01
                    $month = $date->translatedFormat('F'); // Full month name in Indonesian, e.g., Januari
                    $year = $date->format('Y'); // Year, e.g., 2023

                    $asset = inventory::where('asset_code', $formKerusakan->kode_asset)->first();

                    $templatePath = storage_path('app/public/templates/SERVICE.docx');
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', $letter->kode_surat);
                    $templateProcessor->setValue('day', $day);
                    $templateProcessor->setValue('date', $dateFormatted);
                    $templateProcessor->setValue('month', $month);
                    $templateProcessor->setValue('year', $year);
                    $templateProcessor->setValue('kode_asset', $formKerusakan->kode_asset);
                    $templateProcessor->setValue('jenis', $asset->asset_type);
                    $templateProcessor->setValue('merk', $asset->merk);
                    $templateProcessor->setValue('deskripsi', $asset->description);
                    $templateProcessor->setValue('serial', $asset->serial_number);
                    $templateProcessor->setValue('tanggal_perolehan', $asset->acquisition_date);
                    $templateProcessor->setValue('kerusakan', $formKerusakan->kerusakan);
                    $templateProcessor->setValue('penyebab', $formKerusakan->penyebab);
                    $templateProcessor->setValue('nama', $formKerusakan->nama);
                    $templateProcessor->setValue('nik', $formKerusakan->nik);
                    $templateProcessor->setValue('jabatan', $formKerusakan->jabatan);

                    // dd($templateProcessor);

                    $tempFilePath = storage_path('app/FormKerusakanAsset_' . $letter->id . '.docx');
                    $templateProcessor->saveAs($tempFilePath);

                    // Return the document as a download response
                    return response()->download($tempFilePath)->deleteFileAfterSend(true);
                } else {
                    return redirect()->route('form-kerusakan', ['id' => $id]);
                }
            }
        }
    }

    public function showBeritaAcaraForm($id)
    {
        $letter = Letter::findOrFail($id);
        $results = DB::connection('travel')->select('SELECT * FROM employees');

        return view('pages.letter.form-berita-acara', compact('letter', 'results'));
    }

    public function showKerusakanForm($id)
    {
        $letter = Letter::findOrFail($id);
        $results = DB::connection('travel')->select('SELECT * FROM employees');

        return view('pages.letter.form-kerusakan', compact('letter', 'results'));
    }

    public function storeKerusakan(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'kode_asset' => 'required|string|max:255',
            'kerusakan' => 'required|string',
            'penyebab' => 'required|string',
            'tindakan' => 'required|string',
        ]);

        FormKerusakan::create($request->all());

        return redirect()->route('letters.download', $request->letter_id);
    }
}
