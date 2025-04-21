<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
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
                ->addColumn('file', function ($row) {
                    $btn = '';
                    if (empty($row->file)) {
                        $btn .= ' <a href="javascript:void(0)" class="add-document btn btn-primary btn-sm mt-3" data-id="' . $row->id . '"><i class="fas fa-upload"></i> Add Document</a>';
                    } else {
                        $btn .= ' <a href="' . asset('storage/' . $row->file) . '" target="_blank" class="view-document btn btn-success btn-sm mt-3"><i class="fas fa-eye"></i> View Document</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'file'])
                ->make(true);
        }

        return view('pages.letter.index');
    }

    public function addDocument(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        $letter = Letter::findOrFail($request->letter_id);
        try {
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('letters', 'public');

                $letter->update(['file' => $filePath]);
            }

            return redirect()->route('generate-letter')->with('success', 'Data has been added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('generate-letter')->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
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
        } elseif ($jenisBA == 'BAST') {
            // Ambil bulan dalam format Romawi
            $bulanRomawi = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];
            $bulan = $bulanRomawi[\Carbon\Carbon::parse($request->tanggal)->month];
            $tahun = \Carbon\Carbon::parse($request->tanggal)->year;

            // Tentukan kode berdasarkan perihal
            $kodePerihal = '';
            if ($perihal == 'General') {
                $kodePerihal = 'GR';
            } elseif ($perihal == 'Radio') {
                $kodePerihal = 'RD';
            }

            // Ambil iterasi terakhir untuk jenisBA BAST dan perihal tertentu
            $latestLetter = Letter::whereYear('tanggal', $tahun)
                ->where('jenisBA', 'BAST')
                ->where('perihal', $perihal)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestLetter) {
                $latestIterasi = intval(explode('/', $latestLetter->kode_surat)[0]);
                $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $iterasi = '001';
            }

            // Format kode surat
            $kode_surat = "{$iterasi}/{$kodePerihal}/BAST/MLP/{$bulan}/{$tahun}";
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
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'perihal' => 'nullable|string|max:255',
            'jenisBA' => 'required|string|max:255',
        ]);

        $perihal = $request->perihal ?? '-';

        // Temukan surat berdasarkan ID
        $letter = Letter::findOrFail($id);

        // Ambil data tanggal dan tahun
        $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('dm');
        $tahun = \Carbon\Carbon::parse($request->tanggal)->format('Y');

        // Periksa apakah jenisBA adalah FORM KERUSAKAN ASSET dan hanya perihal yang berubah
        if ($request->jenisBA == 'FORM KERUSAKAN ASSET' && $letter->jenisBA == 'FORM KERUSAKAN ASSET') {
            // Jika hanya perihal yang berubah, kode surat tidak diubah
            $kode_surat = $letter->kode_surat;
        } else {
            // Perbarui kode surat berdasarkan jenisBA
            $kode_surat = '';
            if ($request->jenisBA == 'FORM KERUSAKAN ASSET') {
                $bulan = \Carbon\Carbon::parse($request->tanggal)->format('m');

                // Ambil iterasi terakhir untuk FORM KERUSAKAN ASSET
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
            } elseif ($request->jenisBA == 'BAST') {
                // Ambil bulan dalam format Romawi
                $bulanRomawi = [
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII'
                ];
                $bulan = $bulanRomawi[\Carbon\Carbon::parse($request->tanggal)->month];

                // Tentukan kode berdasarkan perihal
                $kodePerihal = $perihal == 'General' ? 'GR' : ($perihal == 'Radio' ? 'RD' : 'OT');

                // Ambil iterasi terakhir untuk jenisBA BAST dan perihal tertentu
                $latestLetter = Letter::whereYear('tanggal', $tahun)
                    ->where('jenisBA', 'BAST')
                    ->where('perihal', $perihal)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestLetter) {
                    $latestIterasi = intval(explode('/', $latestLetter->kode_surat)[0]);
                    $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $iterasi = '001';
                }

                $kode_surat = "{$iterasi}/{$kodePerihal}/BAST/MLP/{$bulan}/{$tahun}";
            } elseif ($request->jenisBA == 'ASSET SERAH TERIMA' || $request->jenisBA == 'ASSET HILANG') {
                // Ambil iterasi terakhir untuk jenisBA ASSET SERAH TERIMA atau ASSET HILANG
                $latestLetter = Letter::whereYear('tanggal', $tahun)
                    ->where('jenisBA', $request->jenisBA)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestLetter) {
                    $latestIterasi = intval(explode('/', $latestLetter->kode_surat)[0]);
                    $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $iterasi = '001';
                }

                $kode_surat = "{$iterasi}/BA/{$tanggal}/{$tahun}/";

                switch ($request->jenisBA) {
                    case 'ASSET SERAH TERIMA':
                        $kode_surat .= 'AST';
                        break;
                    case 'ASSET HILANG':
                        $kode_surat .= 'AH';
                        break;
                    default:
                        $kode_surat .= 'OT'; // Default case
                        break;
                }
            } else {
                // Default untuk jenisBA lainnya
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

                switch ($request->jenisBA) {
                    case 'ASSET RUSAK':
                        $kode_surat .= 'AR';
                        break;
                    case 'ASSET DISPOSE':
                        $kode_surat .= 'AD';
                        break;
                    default:
                        $kode_surat .= 'OT'; // Default case
                        break;
                }
            }
        }

        // Perbarui data surat
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

                    if ($letter->location == 'Head Office') {
                        $templatePath = storage_path('app/public/templates/PEMINJAMAN.docx');
                    } elseif ($letter->location == 'Office Kendari') {
                        $templatePath = storage_path('app/public/templates/PEMINJAMAN_KDI.docx');
                    } elseif ($letter->location == 'Site Molore') {
                        $templatePath = storage_path('app/public/templates/PEMINJAMAN_SITE.docx');
                    }
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                    $templateProcessor->setValue('day', htmlspecialchars($day));
                    $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                    $templateProcessor->setValue('month', htmlspecialchars($month));
                    $templateProcessor->setValue('year', htmlspecialchars($year));

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', htmlspecialchars($firstBeritaAcara->nama));
                    $templateProcessor->setValue('nik', htmlspecialchars($firstBeritaAcara->nik));
                    $templateProcessor->setValue('dept', htmlspecialchars($firstBeritaAcara->dept));
                    $templateProcessor->setValue('jabatan', htmlspecialchars($firstBeritaAcara->jabatan));
                    $templateProcessor->setValue('alamat', htmlspecialchars($firstBeritaAcara->alamat));

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", htmlspecialchars($asset->asset_code ?? ''));
                            $templateProcessor->setValue("description#{$rowNumber}", htmlspecialchars($asset->description ?? ''));
                            $templateProcessor->setValue("serial_number#{$rowNumber}", htmlspecialchars($asset->serial_number ?? ''));
                            $templateProcessor->setValue("tanggal#{$rowNumber}", htmlspecialchars(Carbon::parse($item->tanggal)->format('d-m-Y') ?? ''));
                            $templateProcessor->setValue("alasan#{$rowNumber}", htmlspecialchars($item->alasan ?? ''));
                        }
                    }

                    $templateProcessor->setValue('kronologi', htmlspecialchars($firstBeritaAcara->kronologi));

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

                    if ($letter->location == 'Head Office') {
                        $templatePath = storage_path('app/public/templates/PENGEMBALIAN.docx');
                    } elseif ($letter->location == 'Office Kendari') {
                        $templatePath = storage_path('app/public/templates/PENGEMBALIAN_KDI.docx');
                    } elseif ($letter->location == 'Site Molore') {
                        $templatePath = storage_path('app/public/templates/PENGEMBALIAN_SITE.docx');
                    }
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                    $templateProcessor->setValue('day', htmlspecialchars($day));
                    $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                    $templateProcessor->setValue('month', htmlspecialchars($month));
                    $templateProcessor->setValue('year', htmlspecialchars($year));

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', htmlspecialchars($firstBeritaAcara->nama));
                    $templateProcessor->setValue('nik', htmlspecialchars($firstBeritaAcara->nik));
                    $templateProcessor->setValue('dept', htmlspecialchars($firstBeritaAcara->dept));
                    $templateProcessor->setValue('jabatan', htmlspecialchars($firstBeritaAcara->jabatan));
                    $templateProcessor->setValue('alamat', htmlspecialchars($firstBeritaAcara->alamat));

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", htmlspecialchars($asset->asset_code ?? ''));
                            $templateProcessor->setValue("description#{$rowNumber}", htmlspecialchars($asset->description ?? ''));
                            $templateProcessor->setValue("serial_number#{$rowNumber}", htmlspecialchars($asset->serial_number ?? ''));
                            $templateProcessor->setValue("tanggal#{$rowNumber}", htmlspecialchars(Carbon::parse($item->tanggal)->format('d-m-Y') ?? ''));
                            $templateProcessor->setValue("alasan#{$rowNumber}", htmlspecialchars($item->alasan ?? ''));
                        }
                    }

                    $templateProcessor->setValue('kronologi', htmlspecialchars($firstBeritaAcara->kronologi));

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
                    if ($letter->location == 'Head Office') {
                        $templateProcessor->setValue('location', 'Jakarta');
                    } elseif ($letter->location == 'Office Kendari') {
                        $templateProcessor->setValue('location', 'Kendari');
                    } elseif ($letter->location == 'Site Molore') {
                        $templateProcessor->setValue('location', 'Molore');
                    }
                    $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                    $templateProcessor->setValue('day', htmlspecialchars($day));
                    $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                    $templateProcessor->setValue('month', htmlspecialchars($month));
                    $templateProcessor->setValue('year', htmlspecialchars($year));

                    $firstBeritaAcara = $beritaAcara->first();
                    $templateProcessor->setValue('nama', htmlspecialchars($firstBeritaAcara->nama));
                    $templateProcessor->setValue('dept', htmlspecialchars($firstBeritaAcara->dept));
                    $templateProcessor->setValue('jabatan', htmlspecialchars($firstBeritaAcara->jabatan));
                    $templateProcessor->setValue('nik', htmlspecialchars($firstBeritaAcara->nik));

                    $templateProcessor->setValue('nama2', htmlspecialchars($firstBeritaAcara->nama_2));
                    $templateProcessor->setValue('dept2', htmlspecialchars($firstBeritaAcara->dept_2));
                    $templateProcessor->setValue('jabatan2', htmlspecialchars($firstBeritaAcara->jabatan_2));
                    $templateProcessor->setValue('nik2', htmlspecialchars($firstBeritaAcara->nik_2));

                    // Pastikan berita acara ada
                    if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                        $rowCount = $beritaAcara->count(); // Hitung jumlah data
                        $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                        foreach ($beritaAcara as $index => $item) {
                            $asset = inventory::where('asset_code', $item->no_asset)->first();

                            // Indeks Word mulai dari 1, bukan 0
                            $rowNumber = $index + 1;

                            // Mengisi placeholder dengan data sesuai baris
                            $templateProcessor->setValue("kode_asset#{$rowNumber}", htmlspecialchars($asset->asset_code ?? ''));
                            $templateProcessor->setValue("description#{$rowNumber}", htmlspecialchars($asset->description ?? ''));
                            $templateProcessor->setValue("serial_number#{$rowNumber}", htmlspecialchars($asset->serial_number ?? ''));
                            $templateProcessor->setValue("tanggal#{$rowNumber}", htmlspecialchars(Carbon::parse($item->tanggal)->format('d-m-Y') ?? ''));
                            $templateProcessor->setValue("alasan#{$rowNumber}", htmlspecialchars($item->alasan ?? ''));
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

                if ($letter->location == 'Head Office') {
                    $templatePath = storage_path('app/public/templates/HILANG.docx');
                } elseif ($letter->location == 'Office Kendari') {
                    $templatePath = storage_path('app/public/templates/HILANG_KDI.docx');
                } elseif ($letter->location == 'Site Molore') {
                    $templatePath = storage_path('app/public/templates/HILANG_SITE.docx');
                }
                $templateProcessor = new TemplateProcessor($templatePath);
                $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                $templateProcessor->setValue('day', htmlspecialchars($day));
                $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                $templateProcessor->setValue('month', htmlspecialchars($month));
                $templateProcessor->setValue('year', htmlspecialchars($year));

                $firstBeritaAcara = $beritaAcara->first();
                $templateProcessor->setValue('nama', htmlspecialchars($firstBeritaAcara->nama));
                $templateProcessor->setValue('nik', htmlspecialchars($firstBeritaAcara->nik));
                $templateProcessor->setValue('dept', htmlspecialchars($firstBeritaAcara->dept));
                $templateProcessor->setValue('jabatan', htmlspecialchars($firstBeritaAcara->jabatan));
                $templateProcessor->setValue('alamat', htmlspecialchars($firstBeritaAcara->alamat));

                // Pastikan berita acara ada
                if ($beritaAcara && $beritaAcara instanceof \Illuminate\Database\Eloquent\Collection) {
                    $rowCount = $beritaAcara->count(); // Hitung jumlah data
                    $templateProcessor->cloneRow('kode_asset', $rowCount); // Gandakan baris sesuai jumlah data

                    foreach ($beritaAcara as $index => $item) {
                        $asset = inventory::where('asset_code', $item->no_asset)->first();

                        // Indeks Word mulai dari 1, bukan 0
                        $rowNumber = $index + 1;

                        // Mengisi placeholder dengan data sesuai baris
                        $templateProcessor->setValue("kode_asset#{$rowNumber}", htmlspecialchars($asset->asset_code ?? ''));
                        $templateProcessor->setValue("description#{$rowNumber}", htmlspecialchars($asset->description ?? ''));
                        $templateProcessor->setValue("serial_number#{$rowNumber}", htmlspecialchars($asset->serial_number ?? ''));
                        $templateProcessor->setValue("tanggal#{$rowNumber}", htmlspecialchars(Carbon::parse($item->tanggal)->format('d-m-Y') ?? ''));
                        $templateProcessor->setValue("alasan#{$rowNumber}", htmlspecialchars($item->alasan ?? ''));
                    }
                }

                $templateProcessor->setValue('kronologi', htmlspecialchars($firstBeritaAcara->kronologi));

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

                    if ($letter->location == 'Head Office') {
                        $templatePath = storage_path('app/public/templates/PENGGANTIAN.docx');
                    } elseif ($letter->location == 'Office Kendari') {
                        $templatePath = storage_path('app/public/templates/PENGGANTIAN_KDI.docx');
                    } elseif ($letter->location == 'Site Molore') {
                        $templatePath = storage_path('app/public/templates/PENGGANTIAN_SITE.docx');
                    }
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                    $templateProcessor->setValue('day', htmlspecialchars($day));
                    $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                    $templateProcessor->setValue('month', htmlspecialchars($month));
                    $templateProcessor->setValue('year', htmlspecialchars($year));
                    $templateProcessor->setValue('kode_asset', htmlspecialchars($formKerusakan->kode_asset));
                    $templateProcessor->setValue('jenis', htmlspecialchars($asset->asset_type));
                    $templateProcessor->setValue('merk', htmlspecialchars($asset->merk));
                    $templateProcessor->setValue('deskripsi', htmlspecialchars($asset->description));
                    $templateProcessor->setValue('serial', htmlspecialchars($asset->serial_number));
                    $templateProcessor->setValue('tanggal_perolehan', htmlspecialchars($asset->acquisition_date));
                    $templateProcessor->setValue('kerusakan', htmlspecialchars($formKerusakan->kerusakan));
                    $templateProcessor->setValue('penyebab', htmlspecialchars($formKerusakan->penyebab));
                    $templateProcessor->setValue('harga_perolehan', htmlspecialchars('Rp ' . number_format($asset->acquisition_value, 0, ',', '.')));
                    $templateProcessor->setValue('nama', htmlspecialchars($formKerusakan->nama));
                    $templateProcessor->setValue('nik', htmlspecialchars($formKerusakan->nik));
                    $templateProcessor->setValue('jabatan', htmlspecialchars($formKerusakan->jabatan));

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

                    if ($letter->location == 'Head Office') {
                        $templatePath = storage_path('app/public/templates/SERVICE.docx');
                    } elseif ($letter->location == 'Office Kendari') {
                        $templatePath = storage_path('app/public/templates/SERVICE_KDI.docx');
                    } elseif ($letter->location == 'Site Molore') {
                        $templatePath = storage_path('app/public/templates/SERVICE_SITE.docx');
                    }
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('kode_surat', htmlspecialchars($letter->kode_surat));
                    $templateProcessor->setValue('day', htmlspecialchars($day));
                    $templateProcessor->setValue('date', htmlspecialchars($dateFormatted));
                    $templateProcessor->setValue('month', htmlspecialchars($month));
                    $templateProcessor->setValue('year', htmlspecialchars($year));
                    $templateProcessor->setValue('kode_asset', htmlspecialchars($formKerusakan->kode_asset));
                    $templateProcessor->setValue('jenis', htmlspecialchars($asset->asset_type));
                    $templateProcessor->setValue('merk', htmlspecialchars($asset->merk));
                    $templateProcessor->setValue('deskripsi', htmlspecialchars($asset->description));
                    $templateProcessor->setValue('serial', htmlspecialchars($asset->serial_number));
                    $templateProcessor->setValue('tanggal_perolehan', htmlspecialchars($asset->acquisition_date));
                    $templateProcessor->setValue('kerusakan', htmlspecialchars($formKerusakan->kerusakan));
                    $templateProcessor->setValue('penyebab', htmlspecialchars($formKerusakan->penyebab));
                    $templateProcessor->setValue('nama', htmlspecialchars($formKerusakan->nama));
                    $templateProcessor->setValue('nik', htmlspecialchars($formKerusakan->nik));
                    $templateProcessor->setValue('jabatan', htmlspecialchars($formKerusakan->jabatan));

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

    public function storeBeritaAcara(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'dept' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'no_asset' => 'required|array',
            'no_asset.*' => 'required|string|max:255',
            'tanggal' => 'required|array',
            'tanggal.*' => 'required|date',
            'alasan' => 'required|array',
            'alasan.*' => 'required|string',
            'kronologi' => 'nullable|string',
            'nama_2' => 'nullable|string|max:255',
            'nik_2' => 'nullable|string|max:255',
            'dept_2' => 'nullable|string|max:255',
            'jabatan_2' => 'nullable|string|max:255',
        ]);

        foreach ($request->no_asset as $index => $no_asset) {
            BeritaAcara::create([
                'letter_id' => $request->letter_id,
                'nama' => $request->nama,
                'nik' => $request->nik,
                'dept' => $request->dept,
                'jabatan' => $request->jabatan,
                'alamat' => $request->alamat,
                'no_asset' => $no_asset,
                'tanggal' => $request->tanggal[$index],
                'alasan' => $request->alasan[$index],
                'kronologi' => $request->kronologi,
                'nama_2' => $request->nama_2,
                'nik_2' => $request->nik_2,
                'dept_2' => $request->dept_2,
                'jabatan_2' => $request->jabatan_2,
            ]);
        }

        return redirect()->route('letters.download', $request->letter_id);
    }

    public function viewBastItAsset(Request $request)
    {
        if ($request->ajax()) {
            $basts = DB::connection('itam')->table('basts')->get();

            // Tambahkan data karyawan dari koneksi 'travel'
            $basts = $basts->map(function ($bast) {
                $employee = DB::connection('travel')->table('employees')
                    ->select('nama', 'job_position')
                    ->where('nik', $bast->nik_user)
                    ->first();

                $bast->nama = $employee ? $employee->nama : '-';
                $bast->job_position = $employee ? $employee->job_position : '-';

                return $bast;
            });

            // Kembalikan data tanpa kolom 'action'
            return DataTables::of($basts)
                ->addIndexColumn()
                ->make(true);
        }

        // Tampilkan view untuk halaman "View BAST IT Asset"
        return view('pages.letter.view-bast-it');
    }
}
