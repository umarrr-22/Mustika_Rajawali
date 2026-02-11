<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use App\Models\ServiceMasuk;
use App\Models\RefilMasuk;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Statistics Data
        $stats = [
            'jadwal' => [
                'total' => JadwalKurir::count(),
                'today' => JadwalKurir::whereDate('tanggal', $today)->count(),
                'month' => JadwalKurir::where('tanggal', '>=', $startOfMonth)->count(),
                'completed' => JadwalKurir::where('status', 'Selesai')->count()
            ],
            'service' => [
                'total' => ServiceMasuk::count(),
                'today' => ServiceMasuk::whereDate('created_at', $today)->count(),
                'month' => ServiceMasuk::where('created_at', '>=', $startOfMonth)->count(),
                'pending' => ServiceMasuk::where('status', 'Diproses')->count(),
                'completed' => ServiceMasuk::where('status', 'Selesai')->count()
            ],
            'refil' => [
                'total' => RefilMasuk::count(),
                'today' => RefilMasuk::whereDate('created_at', $today)->count(),
                'month' => RefilMasuk::where('created_at', '>=', $startOfMonth)->count(),
                'pending' => RefilMasuk::where('status', 'Diproses')->count(),
                'completed' => RefilMasuk::where('status', 'Selesai')->count()
            ],
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::where('created_at', '>=', $startOfMonth)->count()
            ]
        ];

        // Latest Activities
        $latest = [
            'jadwals' => JadwalKurir::with('kurir')
                ->whereDate('tanggal', '>=', $today)
                ->orderBy('tanggal')
                ->limit(5)
                ->get(),
            'services' => ServiceMasuk::with('teknisi')
                ->where('status', 'Diproses')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'refils' => RefilMasuk::with('penangan')
                ->where('status', 'Diproses')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        // Chart Data
        $charts = [
            'service_monthly' => $this->getMonthlyData(ServiceMasuk::class),
            'refil_monthly' => $this->getMonthlyData(RefilMasuk::class),
            'status_distribution' => $this->getStatusDistribution()
        ];

        return view('admin.dashboard', compact('stats', 'latest', 'charts'));
    }

    protected function getMonthlyData($model, $months = 12)
    {
        $data = [];
        $labels = [];
        
        for ($i = $months-1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = $model::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getStatusDistribution()
    {
        // Get service status data
        $serviceData = ServiceMasuk::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
        
        $serviceTotal = $serviceData->sum('count');
        $serviceStatuses = [];
        
        foreach ($serviceData as $item) {
            $percentage = $serviceTotal > 0 ? round(($item->count / $serviceTotal) * 100, 1) : 0;
            $serviceStatuses[$item->status] = [
                'count' => $item->count,
                'percentage' => $percentage
            ];
        }

        // Get refil status data
        $refilData = RefilMasuk::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
            
        $refilTotal = $refilData->sum('count');
        $refilStatuses = [];
        
        foreach ($refilData as $item) {
            $percentage = $refilTotal > 0 ? round(($item->count / $refilTotal) * 100, 1) : 0;
            $refilStatuses[$item->status] = [
                'count' => $item->count,
                'percentage' => $percentage
            ];
        }

        // Status colors and icons
        $statusColors = [
            'Diproses' => ['color' => '#f6c23e', 'icon' => 'fas fa-clock'],
            'Selesai' => ['color' => '#1cc88a', 'icon' => 'fas fa-check-circle'],
            'Dibatalkan' => ['color' => '#e74a3b', 'icon' => 'fas fa-times-circle'],
            'Menunggu' => ['color' => '#858796', 'icon' => 'fas fa-hourglass-half']
        ];
        
        return [
            'service' => $this->mapStatusData($serviceStatuses, $statusColors),
            'refil' => $this->mapStatusData($refilStatuses, $statusColors),
            'service_total' => $serviceTotal,
            'refil_total' => $refilTotal
        ];
    }

    protected function mapStatusData($statuses, $colors)
    {
        $result = [];
        foreach ($statuses as $status => $data) {
            $result[$status] = [
                'count' => $data['count'],
                'percentage' => $data['percentage'],
                'color' => $colors[$status]['color'] ?? '#36b9cc',
                'icon' => $colors[$status]['icon'] ?? 'fas fa-circle'
            ];
        }
        return $result;
    }
}