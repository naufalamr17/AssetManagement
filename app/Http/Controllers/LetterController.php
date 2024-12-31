<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Letter;
use Yajra\DataTables\DataTables;

class LetterController extends Controller
{
    public function generate(Request $request)
    {
        if ($request->ajax()) {
            $data = Letter::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-dark btn-sm mt-3"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" class="delete btn btn-danger btn-sm mt-3"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
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
            'perihal' => 'required|string|max:255',
            'jenisBA' => 'required|string|max:255',
        ]);

        // Generate kode_surat based on jenisBA
        $tanggal = \Carbon\Carbon::parse($request->tanggal)->format('dm');
        $tahun = \Carbon\Carbon::parse($request->tanggal)->format('Y');
        $jenisBA = $request->jenisBA;
        // Get the latest iterasi for the current year
        $latestLetter = Letter::whereYear('tanggal', $tahun)->orderBy('id', 'desc')->first();
        if ($latestLetter) {
            $latestIterasi = intval(explode('/', $latestLetter->kode_surat)[0]);
            $iterasi = str_pad($latestIterasi + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $iterasi = '001';
        }
        $kode_surat = "{$iterasi}/BA/{$tanggal}/{$tahun}/";

        switch ($jenisBA) {
            case 'ASSET RUSAK':
                $kode_surat .= 'AR';
                break;
            case 'ASSET DISPOSE':
                $kode_surat .= 'AD';
                break;
            case 'ASSET HILANG':
                $kode_surat .= 'AH';
                break;
            default:
                $kode_surat .= 'AST'; // Default case if needed
                break;
        }

        Letter::create([
            'tanggal' => $request->tanggal,
            'perihal' => $request->perihal,
            'jenisBA' => $request->jenisBA,
            'kode_surat' => $kode_surat,
        ]);

        return redirect()->route('generate-letter')->with('success', 'Data has been added successfully.');
    }
}
