<?php

namespace App\Http\Controllers\Refil;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Hitung statistik refil
            $stats = [
                'masuk' => RefilMasuk::where('status', 'Menunggu')->count(),
                'proses' => RefilMasuk::where('status', 'Diproses')->count(),
                'selesai' => RefilMasuk::where('status', 'Selesai')->count(),
                'total' => RefilMasuk::count(),
            ];

            // Data untuk chart penyelesaian bulanan (6 bulan terakhir)
            $monthlyCompletion = RefilMasuk::selectRaw('
                    DATE_FORMAT(tanggal_selesai, "%Y-%m") as month,
                    COUNT(*) as completed_count,
                    AVG(DATEDIFF(tanggal_selesai, tanggal_masuk)) as avg_days
                ')
                ->where('status', 'Selesai')
                ->where('tanggal_selesai', '>=', Carbon::now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Format data chart bulanan
            $monthlyChartData = [
                'labels' => $monthlyCompletion->map(function ($item) {
                    return Carbon::createFromFormat('Y-m', $item->month)->format('M Y');
                }),
                'completed' => $monthlyCompletion->pluck('completed_count'),
                'avg_days' => $monthlyCompletion->pluck('avg_days')->map(function ($days) {
                    return round($days, 1);
                }),
            ];

            // Data untuk chart aktivitas (bulan ini default)
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

            $dailyStatusData = RefilMasuk::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->selectRaw('DATE(created_at) as date, 
                           SUM(CASE WHEN status = "Menunggu" THEN 1 ELSE 0 END) as masuk,
                           SUM(CASE WHEN status = "Diproses" THEN 1 ELSE 0 END) as proses,
                           SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Format data chart harian
            $chartData = [
                'labels' => $dailyStatusData->map(function ($item) {
                    return Carbon::parse($item->date)->format('d M');
                }),
                'masuk' => $dailyStatusData->pluck('masuk'),
                'proses' => $dailyStatusData->pluck('proses'),
                'selesai' => $dailyStatusData->pluck('selesai'),
                'month' => Carbon::now()->format('F Y')
            ];

            // Data untuk pie chart distribusi status
            $statusDistribution = RefilMasuk::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->orderBy('count', 'desc')
                ->get();

            $pieChartData = [
                'labels' => $statusDistribution->pluck('status'),
                'data' => $statusDistribution->pluck('count'),
                'colors' => ['#0d6efd', '#ffc107', '#198754'] // Warna untuk Menunggu, Diproses, Selesai
            ];

            // Data untuk chart jenis layanan
            $serviceTypeData = RefilMasuk::selectRaw('jenis_layanan, COUNT(*) as count')
                ->groupBy('jenis_layanan')
                ->orderBy('count', 'desc')
                ->get();

            $serviceChartData = [
                'labels' => $serviceTypeData->pluck('jenis_layanan'),
                'data' => $serviceTypeData->pluck('count'),
                'colors' => ['#0dcaf0', '#fd7e14'] // Warna untuk Refil dan lainnya
            ];

            // Ambil data refil terbaru
            $refilMasuk = RefilMasuk::where('status', 'Menunggu')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $refilDiproses = RefilMasuk::where('status', 'Diproses')
                ->orderBy('tanggal_masuk', 'asc')
                ->take(5)
                ->get();

            $refilSelesai = RefilMasuk::where('status', 'Selesai')
                ->orderBy('tanggal_selesai', 'desc')
                ->take(5)
                ->get();

            return view('refil.dashboard', [
                'stats' => $stats,
                'refilMasuk' => $refilMasuk,
                'refilDiproses' => $refilDiproses,
                'refilSelesai' => $refilSelesai,
                'chartData' => $chartData,
                'pieChartData' => $pieChartData,
                'serviceChartData' => $serviceChartData,
                'monthlyChartData' => $monthlyChartData,
            ]);

        } catch (\Exception $e) {
            Log::error('Error di DashboardController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat dashboard');
        }
    }

    public function getChartData(Request $request)
    {
        $range = $request->input('range', 'month');
        
        switch ($range) {
            case '7':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $rangeText = '7 Hari Terakhir';
                break;
            case '30':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $rangeText = '30 Hari Terakhir';
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $rangeText = Carbon::now()->subMonth()->format('F Y');
                break;
            case 'month':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $rangeText = Carbon::now()->format('F Y');
                break;
        }

        $dailyStatusData = RefilMasuk::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, 
                       SUM(CASE WHEN status = "Menunggu" THEN 1 ELSE 0 END) as masuk,
                       SUM(CASE WHEN status = "Diproses" THEN 1 ELSE 0 END) as proses,
                       SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $dailyStatusData->map(function ($item) {
                return Carbon::parse($item->date)->format('d M');
            }),
            'masuk' => $dailyStatusData->pluck('masuk'),
            'proses' => $dailyStatusData->pluck('proses'),
            'selesai' => $dailyStatusData->pluck('selesai'),
            'range_text' => $rangeText
        ]);
    }
}