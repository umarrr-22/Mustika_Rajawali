<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $teknisiId = Auth::id();
            
            // Hitung statistik service
            $stats = [
                'masuk' => ServiceMasuk::where('status', 'Menunggu')
                            ->whereNull('teknisi_id')
                            ->count(),
                'diproses' => ServiceMasuk::where('status', 'Diproses')
                                  ->where('teknisi_id', $teknisiId)
                                  ->count(),
                'selesai' => ServiceMasuk::where('status', 'Selesai')
                                ->where('teknisi_id', $teknisiId)
                                ->count(),
                'total' => ServiceMasuk::where('teknisi_id', $teknisiId)
                                ->count(),
            ];

            // Data untuk chart harian (7 hari terakhir)
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $dailyData = ServiceMasuk::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, 
                           SUM(CASE WHEN status = "Menunggu" THEN 1 ELSE 0 END) as menunggu,
                           SUM(CASE WHEN status = "Diproses" THEN 1 ELSE 0 END) as diproses,
                           SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $dailyChartData = [
                'labels' => $dailyData->map(function ($item) {
                    return Carbon::parse($item->date)->format('d M');
                }),
                'menunggu' => $dailyData->pluck('menunggu'),
                'diproses' => $dailyData->pluck('diproses'),
                'selesai' => $dailyData->pluck('selesai'),
            ];

            // Data untuk chart status - mengambil data menunggu seperti di aktivitas service
            $statusChartData = [
                'labels' => ['Menunggu', 'Diproses', 'Selesai'],
                'data' => [
                    $stats['masuk'],  // Menunggu dari semua service yang belum ditangani
                    $stats['diproses'], // Diproses oleh teknisi ini
                    $stats['selesai']    // Selesai oleh teknisi ini
                ],
                'colors' => ['#6c757d', '#0d6efd', '#198754'] // Grey, Blue, Green
            ];

            // Data untuk chart jenis layanan
            $serviceTypeData = ServiceMasuk::where('teknisi_id', $teknisiId)
                ->selectRaw('jenis_layanan, COUNT(*) as count')
                ->groupBy('jenis_layanan')
                ->orderBy('count', 'desc')
                ->get();

            $serviceTypeChartData = [
                'labels' => $serviceTypeData->pluck('jenis_layanan'),
                'data' => $serviceTypeData->pluck('count'),
                'colors' => ['#0dcaf0', '#fd7e14'] // Warna untuk Service dan Komplain
            ];

            // Data bulanan
            $monthlyData = $this->getMonthlyServiceData($teknisiId);

            // Ambil 5 service terbaru yang sedang diproses
            $latestServices = ServiceMasuk::with(['user'])
                                ->where('teknisi_id', $teknisiId)
                                ->where('status', 'Diproses')
                                ->orderBy('updated_at', 'desc')
                                ->limit(5)
                                ->get();

            return view('teknisi.dashboard', [
                'stats' => $stats,
                'latestServices' => $latestServices ?? collect(),
                'dailyChartData' => $dailyChartData,
                'statusChartData' => $statusChartData,
                'serviceTypeChartData' => $serviceTypeChartData,
                'monthlyLabels' => $monthlyData['labels'] ?? [],
                'monthlyServiceData' => $monthlyData['service'] ?? [],
                'monthlyKomplainData' => $monthlyData['komplain'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error in Teknisi DashboardController: ' . $e->getMessage());
            return view('teknisi.dashboard', [
                'stats' => ['masuk' => 0, 'diproses' => 0, 'selesai' => 0, 'total' => 0],
                'latestServices' => collect(),
                'dailyChartData' => ['labels' => [], 'menunggu' => [], 'diproses' => [], 'selesai' => []],
                'statusChartData' => [
                    'labels' => ['Menunggu', 'Diproses', 'Selesai'], 
                    'data' => [0, 0, 0], 
                    'colors' => ['#6c757d', '#0d6efd', '#198754']
                ],
                'serviceTypeChartData' => ['labels' => [], 'data' => [], 'colors' => []],
                'monthlyLabels' => [],
                'monthlyServiceData' => [],
                'monthlyKomplainData' => []
            ]);
        }
    }

    private function getMonthlyServiceData($teknisiId)
    {
        try {
            $now = Carbon::now();
            $months = [];
            $serviceData = [];
            $komplainData = [];

            // Ambil data 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $months[] = $month->format('M Y');
                
                $start = $month->startOfMonth()->toDateTimeString();
                $end = $month->endOfMonth()->toDateTimeString();

                $serviceData[] = ServiceMasuk::where('teknisi_id', $teknisiId)
                                    ->where('jenis_layanan', 'Service')
                                    ->whereBetween('created_at', [$start, $end])
                                    ->count();

                $komplainData[] = ServiceMasuk::where('teknisi_id', $teknisiId)
                                     ->where('jenis_layanan', 'Komplain')
                                     ->whereBetween('created_at', [$start, $end])
                                     ->count();
            }

            return [
                'labels' => $months,
                'service' => $serviceData,
                'komplain' => $komplainData
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'service' => [],
                'komplain' => []
            ];
        }
    }

    public function getChartData(Request $request)
    {
        $range = $request->input('range', '7');
        
        switch ($range) {
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $rangeText = Carbon::now()->format('F Y');
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $rangeText = Carbon::now()->subMonth()->format('F Y');
                break;
            case '30':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $rangeText = '30 Hari Terakhir';
                break;
            case '7':
            default:
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $rangeText = '7 Hari Terakhir';
                break;
        }

        $dailyData = ServiceMasuk::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, 
                       SUM(CASE WHEN status = "Menunggu" THEN 1 ELSE 0 END) as menunggu,
                       SUM(CASE WHEN status = "Diproses" THEN 1 ELSE 0 END) as diproses,
                       SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $dailyData->map(function ($item) {
                return Carbon::parse($item->date)->format('d M');
            }),
            'menunggu' => $dailyData->pluck('menunggu'),
            'diproses' => $dailyData->pluck('diproses'),
            'selesai' => $dailyData->pluck('selesai'),
            'range_text' => $rangeText
        ]);
    }
}