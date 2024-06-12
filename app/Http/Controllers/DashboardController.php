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
        $yearlyGrowth = $assets->groupBy(function ($item) {
            return Carbon::parse($item->acquisition_date)->format('Y');
        })->map->count();

        // Convert to a format suitable for charts (if necessary)
        $yearlyGrowthFormatted = $yearlyGrowth->sortKeys()->map(function ($count, $year) {
            return ['year' => $year, 'count' => $count];
        })->values();

        // Aggregate data for asset growth per month in the last year
        $oneYearAgo = Carbon::now()->subYear();
        $monthlyGrowth = $assets->filter(function ($item) use ($oneYearAgo) {
            return Carbon::parse($item->acquisition_date)->greaterThanOrEqualTo($oneYearAgo);
        })->groupBy(function ($item) {
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

        // dd($monthlyGrowthFormatted);

        return view('dashboard.index', [
            'statusCounts' => $statusCounts,
            'categoryStatusCounts' => $categoryStatusCounts,
            'yearlyGrowth' => $yearlyGrowthFormatted,
            'monthlyGrowth' => $monthlyGrowthFormatted,
        ]);
    }
}
