<?php

namespace App\Http\Controllers;

use App\Models\inventory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $assets = inventory::all();

        // Aggregate data for the charts
        $statusCounts = $assets->groupBy('status')->map->count();
        $categoryStatusCounts = $assets->groupBy('asset_category')->map(function ($category) {
            return $category->groupBy('status')->map->count();
        });

        // Aggregate data for asset growth per year
        $yearlyGrowth = $assets->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->acquisition_date)->format('Y');
        })->map->count();

        // Convert to a format suitable for charts (if necessary)
        $yearlyGrowthFormatted = $yearlyGrowth->sortKeys()->map(function ($count, $year) {
            return ['year' => $year, 'count' => $count];
        })->values();

        // dd($yearlyGrowthFormatted);

        return view('dashboard.index', [
            'statusCounts' => $statusCounts,
            'categoryStatusCounts' => $categoryStatusCounts,
            'yearlyGrowth' => $yearlyGrowthFormatted,
        ]);
    }
}
