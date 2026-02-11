<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use App\Models\RefilMasuk;
use App\Models\ServiceMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Get filter from request or default to 'week'
        $filter = $request->get('filter', 'week');
        
        // Set date range based on filter
        if ($filter === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $groupByFormat = '%Y-%m-%d'; // Group by day for monthly view
            $chartLabelFormat = 'd M'; // Display format for labels
        } elseif ($filter === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $groupByFormat = '%Y-%m'; // Group by month for yearly view
            $chartLabelFormat = 'M Y'; // Display format for labels
        } else {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $groupByFormat = '%Y-%m-%d'; // Group by day for weekly view
            $chartLabelFormat = 'D, d M'; // Display format for labels
        }

        // Count data for cards
        $counts = [
            'todaySchedules' => JadwalKurir::where('kurir_id', $userId)
                ->whereDate('tanggal', $today)
                ->count(),
                
            'completedToday' => JadwalKurir::where('kurir_id', $userId)
                ->where(function($query) {
                    $query->where('status', 'selesai')
                          ->orWhereNotNull('completed_at');
                })
                ->whereDate('completed_at', $today)
                ->count(),
                
            'pendingRefils' => RefilMasuk::where('user_id', $userId)
                ->where('status', 'Draft')
                ->count(),
                
            'pendingServices' => ServiceMasuk::where('user_id', $userId)
                ->where('status', 'Draft')
                ->count(),
                
            'currentFilter' => $filter,
            'totalSchedules' => JadwalKurir::where('kurir_id', $userId)->count(),
            'totalCompleted' => JadwalKurir::where('kurir_id', $userId)
                ->where(function($query) {
                    $query->where('status', 'selesai')
                          ->orWhereNotNull('completed_at');
                })->count()
        ];

        // Data for schedule chart - fixed query
        $schedulesQuery = JadwalKurir::where('kurir_id', $userId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(tanggal, '{$groupByFormat}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $schedules = $schedulesQuery->pluck('count', 'date');

        // Data for completed chart - fixed query
        $completedQuery = JadwalKurir::where('kurir_id', $userId)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where(function($query) {
                $query->where('status', 'selesai')
                      ->orWhereNotNull('completed_at');
            })
            ->selectRaw("DATE_FORMAT(completed_at, '{$groupByFormat}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $completed = $completedQuery->pluck('count', 'date');

        // Get latest 5 schedules for today
        $latestSchedules = JadwalKurir::where('kurir_id', $userId)
            ->whereDate('tanggal', $today)
            ->orderBy('tanggal', 'asc')
            ->limit(5)
            ->get();

        // Prepare chart data with fallback for empty data
        $chartData = [
            'schedules' => $this->prepareChartData($schedules, $startDate, $endDate, $filter, $chartLabelFormat),
            'completed' => $this->prepareChartData($completed, $startDate, $endDate, $filter, $chartLabelFormat),
        ];

        // Debug data - bisa dihapus setelah testing
        logger()->info('Chart Data:', $chartData);

        return view('kurir.dashboard', array_merge($counts, $chartData, [
            'latestSchedules' => $latestSchedules,
            'todayDate' => Carbon::today()->format('d F Y')
        ]));
    }

    private function prepareChartData($data, $startDate, $endDate, $filter, $labelFormat)
    {
        $interval = $filter === 'year' ? 'P1M' : 'P1D'; // Monthly for year, daily for others
        $endDate = clone $endDate; // Avoid modifying the original
        
        if ($filter === 'month' || $filter === 'week') {
            $endDate->addDay(); // Include end date
        }

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval($interval),
            $endDate
        );

        $labels = [];
        $chartData = [];

        foreach ($period as $date) {
            $dateString = $filter === 'year' ? $date->format('Y-m') : $date->format('Y-m-d');
            $labels[] = $date->format($labelFormat);
            $chartData[] = $data[$dateString] ?? 0;
        }

        // Ensure we have at least one data point
        if (empty($labels)) {
            $labels[] = Carbon::now()->format($labelFormat);
            $chartData[] = 0;
        }

        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }
}