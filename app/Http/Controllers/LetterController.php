<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Letter;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

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

        $beritaAcara = $letter->beritaAcara()->first();

        $formKerusakan = $letter->formKerusakan()->first();

        // dd($letter, $beritaAcara, $formKerusakan);

        if ($letter->jenisBA == 'ASSET SERAH TERIMA') {
            if ($letter->perihal == 'PEMINJAMAN ASSET') {
                dd('PEMINJAMAN ASSET', $letter);
            } elseif ($letter->perihal == 'PENGEMBALIAN ASSET') {
                dd('PENGEMBALIAN ASSET', $letter);
            } elseif ($letter->perihal == 'MUTASI ASSET') {
                dd('MUTASI ASSET', $letter);
            }
        } elseif ($letter->jenisBA == 'ASSET HILANG') {
            dd('ASSET HILANG', $letter);
        } elseif ($letter->jenisBA == 'FORM KERUSAKAN ASSET') {
            if ($letter->perihal == 'PENGGANTIAN ASSET') {
                dd('PENGGANTIAN ASSET', $letter);
            } elseif ($letter->perihal == 'PERBAIKAN ASSET') {
                dd('PERBAIKAN ASSET', $letter);
            }
        }
    }
}
