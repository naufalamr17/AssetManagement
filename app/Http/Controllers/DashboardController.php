<?php

namespace App\Http\Controllers;

use App\Models\inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $assets = Inventory::all();

        // Aggregate data for the charts
        $statusCounts = $assets->groupBy('status')->map->count();
        $categoryStatusCounts = $assets->groupBy('asset_category')->map(function ($category) {
            return $category->groupBy('status')->map->count();
        });

        // Aggregate data for asset growth per year
        $yearlyGrowth = $assets->filter(function ($item) {
            // Filter hanya data dengan acquisition_date tidak kosong
            return $item->acquisition_date !== '-';
        })->groupBy(function ($item) {
            // Menggunakan Carbon untuk mengurai acquisition_date yang valid
            return Carbon::parse($item->acquisition_date)->format('Y');
        })->map->count();

        // Convert to a format suitable for charts (if necessary)
        $yearlyGrowthFormatted = $yearlyGrowth->sortKeys()->map(function ($count, $year) {
            return ['year' => $year, 'count' => $count];
        })->values();

        // Aggregate data for asset growth per month in the last year
        $oneYearAgo = Carbon::now()->subYear();
        $monthlyGrowth = $assets->filter(function ($item) use ($oneYearAgo) {
            // Filter hanya data dengan acquisition_date tidak sama dengan '-'
            return $item->acquisition_date !== '-' && Carbon::parse($item->acquisition_date)->greaterThanOrEqualTo($oneYearAgo);
        })->groupBy(function ($item) {
            // Menggunakan Carbon untuk mengurai acquisition_date yang valid
            return Carbon::parse($item->acquisition_date)->format('Y-m');
        })->map->count();

        // Ensure every month in the last year is represented, even if the count is zero
        $monthlyGrowthFormatted = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->format('Y-m');
            $monthlyGrowthFormatted->push([
                'month' => $date,
                'count' => $monthlyGrowth->get($date, 0)
            ]);
        }
        $monthlyGrowthFormatted = $monthlyGrowthFormatted->sortBy('month')->values();

        $inventory = inventory::join('disposes', 'inventories.id', '=', 'disposes.inv_id')
            ->select(
                'inventories.asset_code',
                'inventories.asset_type',
                'inventories.serial_number',
                'inventories.useful_life',
                'inventories.location',
                'inventories.status',
                'disposes.tanggal_penghapusan',
                'disposes.note'
            )
            ->orderBy('disposes.tanggal_penghapusan', 'desc')
            ->take(5)
            ->get();

        $repair = inventory::join('repairstatuses', 'inventories.id', '=', 'repairstatuses.inv_id')
            ->select(
                'inventories.asset_code',
                'inventories.asset_type',
                'inventories.serial_number',
                'inventories.useful_life',
                'inventories.location',
                'repairstatuses.status',
                'repairstatuses.tanggal_kerusakan',
                'repairstatuses.tanggal_pengembalian',
                'repairstatuses.note'
            )
            ->orderBy('repairstatuses.tanggal_kerusakan', 'desc')
            ->take(5)
            ->get();

        // dd($monthlyGrowthFormatted);

        return view('dashboard.index', [
            'statusCounts' => $statusCounts,
            'categoryStatusCounts' => $categoryStatusCounts,
            'yearlyGrowth' => $yearlyGrowthFormatted,
            'monthlyGrowth' => $monthlyGrowthFormatted,
            'inventory' => $inventory,
            'repair' => $repair
        ]);
    }
}
